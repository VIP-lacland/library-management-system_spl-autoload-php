/* =========================
   DROP & CREATE DATABASE
   ========================= */
-- DROP DATABASE IF EXISTS library_management;

CREATE DATABASE IF NOT EXISTS library_management
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE library_management;

-- DROP TABLE book_items, books, categories, loans, users;


-- Tạo database
CREATE DATABASE IF NOT EXISTS library_management;
USE library_management;

-- Tạo bảng Users
CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'reader') NOT NULL DEFAULT 'reader',
    status ENUM('active', 'block') NOT NULL DEFAULT 'active',
    phone INT,
    address VARCHAR(255)
);

-- Tạo bảng Categories
CREATE TABLE Categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- Tạo bảng Books
CREATE TABLE Books (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    category_id INT,
    publisher VARCHAR(100),
    publish_year INT,
    description TEXT,
    author VARCHAR(100),
    url VARCHAR(250),
    FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE SET NULL
);

-- Tạo bảng Book_Items
CREATE TABLE Book_Items (
    book_items_id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    status ENUM('available', 'borrowed', 'lost', 'damaged') NOT NULL DEFAULT 'available',
    barcode INT UNIQUE,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE
);

-- Tạo bảng Loans
CREATE TABLE Loans (
    loan_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_items_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    status ENUM('pending', 'borrowing', 'returned', 'overdue', 'renewal', 'rejected') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_items_id) REFERENCES Book_Items(book_items_id) ON DELETE CASCADE
);


-- Insert data into Users table
-- Insert data into Users table
INSERT INTO Users (name, email, password, role, status, phone, address) VALUES
('John Smith', 'admin@library.com', 'admin123', 'admin', 'active', 901234567, '123 Main Street, District 1, Ho Chi Minh City'),
('Emily Johnson', 'emily.johnson@email.com', 'emily2024', 'reader', 'active', 912345678, '456 Broadway Avenue, District 1, Ho Chi Minh City'),
('Michael Brown', 'michael.brown@email.com', 'michael456', 'reader', 'active', 923456789, '789 Oak Street, District 5, Ho Chi Minh City'),
('Sarah Davis', 'sarah.davis@email.com', 'sarah789', 'reader', 'active', 934567890, '321 Pine Road, District 10, Ho Chi Minh City'),
('David Wilson', 'david.wilson@email.com', 'david2024', 'reader', 'block', 945678901, '654 Elm Avenue, District 3, Ho Chi Minh City'),
('Jessica Martinez', 'jessica.martinez@email.com', 'jessica123', 'reader', 'active', 956789012, '987 Maple Street, Tan Binh District, Ho Chi Minh City');

-- Insert data into Categories table
INSERT INTO Categories (name) VALUES
('Literature'),
('Science'),
('History'),
('Technology'),
('Economics'),
('Business'),
('Arts'),
('Children'),
('Education');


INSERT INTO Books (title, category_id, publisher, publish_year, description, author, url) VALUES
('Being Digital', 4, 'Alfred A. Knopf, Inc', 1995, 'A classic tech book exploring how digital technology reshapes all parts of life — from bits vs. atoms to the digital future of media, communication, and society. It’s less about code and more about the philosophy of living in a digital world', 'Nicholas Negroponte', '../../public/images/Technology/tech1.jpg'),
('Life 3.0: Being Human in the Age of Artificial Intelligence', 4, 'Alfred A. Knopf (US); Allen Lane (UK)', 2017, 'A deep-dive into the future of AI — how it works, potential impacts on society, jobs, ethics, and the pathways toward positive (or risky) futures with intelligent machines', 'Max Tegmark', '../../public/images/Technology/tech2.jpg'),
('Revolution in the Valley: The Insanely Great Story of How the Mac Was Made', 4, 'O’Reilly Media', 2004, 'Insider recount of how the Apple Macintosh was developed — a mix of technical storytelling and personal anecdotes from an original member of the Mac team. Cool for history/tech culture nerds', 'Andy Hertzfeld', '../../public/images/Technology/tech3.jpg'),
('A Brief History of Time', 2, 'Bantam Dell Publishing Group', 1988, 'Explains the universe, time, black holes, and the Big Bang in an accessible way for general readers. One of the most famous popular science books of the 20th century', 'Stephen Hawking', '../../public/images/Science/science1.jpg'),
('Fundamentals: Ten Keys to Reality', 2, 'Penguin Random House', 2021, 'Nobel laureate Frank Wilczek presents ten fundamental principles that underlie reality, from space and time to the structure of matter, in an accessible way for curious minds', 'Frank Wilczek', '../../public/images/Science/science2.jpg'),
('Physics of the Future', 2, 'Vintage / Doubleday', 2011, 'Predicts how science and tech like AI, space travel, and medicine might evolve by the year 2100, based on interviews with top scientists', 'Michio Kaku', '../../public/images/Science/science3.jpg'),
('How to Win Friends and Influence People', 7, 'Simon & Schuster', 1936, 'A timeless classic on interpersonal skills, leadership, and influence in business and life. Teaches how to connect with people effectively and build better relationships — core for networking, sales, and team leadership', 'Dale Carnegie', '../../public/images/Business/business1.jpg'),
('Shoe Dog: A Memoir by the Creator of Nike', 7, 'Simon & Schuster / Scribner', 2016, 'A candid memoir from Nike’s co‑founder documenting the messy, chaotic road to building one of the world’s most iconic brands. It highlights persistence, entrepreneurship, and risk in the real business world', 'Phil Knight', '../../public/images/Business/business2.jpg'),
('The Seven-Day Weekend', 7, 'Century', 2003, 'Challenges traditional work models by advocating for flexible, self-managed workplaces. Offers a radical perspective on work culture and innovation', 'Ricardo Semler', '../../public/images/Business/business3.jpg'),
('The Science of Success', 7, 'John Wiley & Sons', 2007, 'Explains Market‑Based Management (MBM) principles used to grow Koch Industries into a global powerhouse. Focuses on decision‑making, incentives, and organizational value creation', 'Charles G. Koch', '../../public/images/Business/business4.jpg');



-- Insert data into Book_Items table (each book has 2-3 copies)
INSERT INTO Book_Items (book_id, status, barcode) VALUES
-- To Kill a Mockingbird
(1, 'available', 1001001),
(1, 'borrowed', 1001002),
(1, 'available', 1001003),
-- The Alchemist
(2, 'borrowed', 1002001),
(2, 'available', 1002002),
-- Introduction to Physics
(3, 'available', 1003001),
(3, 'available', 1003002),
(3, 'borrowed', 1003003),
-- World History Encyclopedia
(4, 'available', 1004001),
(4, 'available', 1004002),
-- Python Programming
(5, 'borrowed', 1005001),
(5, 'available', 1005002),
(5, 'available', 1005003),
-- Principles of Microeconomics
(6, 'available', 1006001),
(6, 'damaged', 1006002),
-- How to Win Friends and Influence People
(7, 'borrowed', 1007001),
(7, 'available', 1007002),
(7, 'available', 1007003),
-- The Power of Now
(8, 'available', 1008001),
(8, 'available', 1008002),
-- Charlotte's Web
(9, 'lost', 1009001),
(9, 'available', 1009002),
-- Artificial Intelligence Basics
(10, 'available', 1010001),
(10, 'borrowed', 1010002);

-- Insert data into Loans table
INSERT INTO Loans (user_id, book_items_id, borrow_date, due_date, return_date, status) VALUES
-- Returned
(2, 1, '2025-12-01', '2025-12-15', '2025-12-14', 'returned'),
(3, 3, '2025-12-05', '2025-12-19', '2025-12-18', 'returned'),
-- Currently borrowing
(2, 2, '2026-01-05', '2026-01-19', NULL, 'borrowing'),
(3, 4, '2026-01-06', '2026-01-20', NULL, 'borrowing'),
(4, 8, '2026-01-07', '2026-01-21', NULL, 'borrowing'),
(6, 11, '2026-01-08', '2026-01-22', NULL, 'borrowing'),
-- Overdue
(2, 15, '2025-12-20', '2026-01-03', NULL, 'overdue'),
(4, 23, '2025-12-25', '2026-01-08', NULL, 'overdue'),
-- Renewed
(3, 6, '2025-12-28', '2026-01-11', NULL, 'renewal'),
-- Additional loan history
(2, 7, '2025-11-15', '2025-11-29', '2025-11-28', 'returned'),
(3, 9, '2025-11-20', '2025-12-04', '2025-12-03', 'returned'),
(4, 13, '2025-11-25', '2025-12-09', '2025-12-08', 'returned'),
(6, 16, '2025-12-01', '2025-12-15', '2025-12-14', 'returned');

-- Tạo bảng Password Resets
CREATE TABLE PasswordResets (
    reset_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    reset_token VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Check inserted data
SELECT 'Users' as Table_Name, COUNT(*) as Total FROM Users
UNION ALL
SELECT 'Categories', COUNT(*) FROM Categories
UNION ALL
SELECT 'Books', COUNT(*) FROM Books
UNION ALL
SELECT 'Book_Items', COUNT(*) FROM Book_Items
UNION ALL
SELECT 'Loans', COUNT(*) FROM Loans;


ALTER TABLE Loans 
ADD COLUMN renewal_count INT DEFAULT 0 AFTER status;

UPDATE Loans 
SET due_date = DATE_ADD(due_date, INTERVAL 7 DAY), 
    renewal_count = renewal_count + 1 
WHERE loan_id = [ID] AND renewal_count < 2;