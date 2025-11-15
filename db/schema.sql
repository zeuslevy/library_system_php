DROP DATABASE IF EXISTS db_library;
CREATE DATABASE db_library CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_library;

-- ============================================
-- 🧑 TABLA: users
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE, 
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','librarian','student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- 📘 TABLA: books
-- ============================================
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    authors VARCHAR(200),
    isbn VARCHAR(20) UNIQUE,
    category VARCHAR(100),
    year_published SMALLINT UNSIGNED,
    copies_total INT DEFAULT 1,
    copies_available INT DEFAULT 1,
    cover_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- 📗 TABLA: loans (préstamos)
-- ============================================
CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    loan_date DATE DEFAULT (CURRENT_DATE),
    return_date DATE GENERATED ALWAYS AS (DATE_ADD(loan_date, INTERVAL 14 DAY)) STORED,
    returned_at DATE DEFAULT NULL,
    fine DECIMAL(6,2) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 🧩 TRIGGERS AUTOMÁTICOS DE COPIAS DISPONIBLES
-- ============================================

DELIMITER $$

-- 🔽 Disminuye la cantidad de copias disponibles al registrar un préstamo
CREATE TRIGGER trg_loan_insert
AFTER INSERT ON loans
FOR EACH ROW
BEGIN
    UPDATE books
    SET copies_available = GREATEST(copies_available - 1, 0)
    WHERE id = NEW.book_id;
END$$

-- 🔼 Aumenta la cantidad de copias disponibles cuando un libro es devuelto
CREATE TRIGGER trg_loan_update
AFTER UPDATE ON loans
FOR EACH ROW
BEGIN
    IF NEW.returned_at IS NOT NULL AND OLD.returned_at IS NULL THEN
        UPDATE books
        SET copies_available = LEAST(copies_available + 1, copies_total)
        WHERE id = NEW.book_id;
    END IF;
END$$

DELIMITER ;

-- ============================================
-- 📚 DATOS DE EJEMPLO: LIBROS
-- ============================================
INSERT INTO books (title, authors, isbn, category, year_published, copies_total, copies_available, cover_url)
VALUES
('Cien años de soledad', 'Gabriel García Márquez', '9788437604947', 'Literatura', 1967, 5, 5, 'https://covers.openlibrary.org/b/id/8776956-L.jpg'),
('El Principito', 'Antoine de Saint-Exupéry', '9780156012195', 'Infantil', 1943, 4, 4, 'https://covers.openlibrary.org/b/id/11153275-L.jpg'),
('1984', 'George Orwell', '9780451524935', 'Distopía', 1949, 3, 3, 'https://covers.openlibrary.org/b/id/7222246-L.jpg'),
('Don Quijote de la Mancha', 'Miguel de Cervantes', '9788491050294', 'Clásico', 1605, 2, 2, 'https://covers.openlibrary.org/b/id/8106873-L.jpg'),
('Crónica de una muerte anunciada', 'Gabriel García Márquez', '9780307388937', 'Realismo mágico', 1981, 3, 3, 'https://covers.openlibrary.org/b/id/8235111-L.jpg');

-------------------
-- ============================================
-- 📕 DATOS DE EJEMPLO: PRÉSTAMOS
-- ============================================
INSERT INTO loans (user_id, book_id, loan_date, returned_at, fine)
VALUES
(3, 1, DATE_SUB(CURDATE(), INTERVAL 10 DAY), NULL, 0.00),
(3, 2, DATE_SUB(CURDATE(), INTERVAL 25 DAY), DATE_SUB(CURDATE(), INTERVAL 10 DAY), 0.00),
(3, 3, DATE_SUB(CURDATE(), INTERVAL 20 DAY), NULL, 3.00);

-- ============================================
-- 🧮 VISTA OPCIONAL: préstamos activos
-- ============================================
CREATE OR REPLACE VIEW view_active_loans AS
SELECT 
    l.id AS loan_id,
    u.name AS user_name,
    b.title AS book_title,
    l.loan_date,
    l.return_date,
    DATEDIFF(CURDATE(), l.return_date) AS dias_atraso,
    CASE 
        WHEN l.returned_at IS NOT NULL THEN 'Devuelto'
        WHEN CURDATE() > l.return_date THEN 'Atrasado'
        ELSE 'Pendiente'
    END AS estado
FROM loans l
JOIN users u ON l.user_id = u.id
JOIN books b ON l.book_id = b.id;

-- ============================================
-- ✅ FIN DEL SCRIPT
-- ============================================
