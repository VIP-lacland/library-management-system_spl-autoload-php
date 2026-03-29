/* =========================
   DROP & CREATE DATABASE
   ========================= */
CREATE DATABASE IF NOT EXISTS library_management
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
-- drop database library_management;
USE library_management;

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


CREATE TABLE Categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);


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


CREATE TABLE Book_Items (
    book_items_id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    status ENUM('available', 'borrowed', 'lost', 'damaged') NOT NULL DEFAULT 'available',
    barcode INT UNIQUE,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE
);


CREATE TABLE Loans (
    loan_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_items_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE,
    return_date DATE,
    status ENUM('pending', 'borrowing', 'returned', 'overdue', 'renewal', 'rejected') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_items_id) REFERENCES Book_Items(book_items_id) ON DELETE CASCADE
);


CREATE TABLE Cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book (user_id, book_id)
);


/* =========================
   INSERT DATA
   ========================= */

-- Insert data into Users table
INSERT INTO Users (name, email, password, role, status, phone, address) VALUES
('John Smith', 'admin@library.com', 'admin2026', 'admin', 'active', 901234567, '123 Main Street, District 1, Ho Chi Minh City'),
('Emily Johnson', 'emily.johnson@email.com', 'emily2024', 'reader', 'active', 912345678, '456 Broadway Avenue, District 1, Ho Chi Minh City'),
('Michael Brown', 'michael.brown@email.com', 'michael456', 'reader', 'active', 923456789, '789 Oak Street, District 5, Ho Chi Minh City'),
('Sarah Davis', 'sarah.davis@email.com', 'sarah789', 'reader', 'active', 934567890, '321 Pine Road, District 10, Ho Chi Minh City'),
('David Wilson', 'david.wilson@email.com', 'david2024', 'reader', 'block', 945678901, '654 Elm Avenue, District 3, Ho Chi Minh City'),
('Jessica Martinez', 'jessica.martinez@email.com', 'jessica123', 'reader', 'active', 956789012, '987 Maple Street, Tan Binh District, Ho Chi Minh City'),
('Anthony Lee', 'anthony.lee@email.com', 'anthony321', 'reader', 'active', 967890123, '111 Cherry Lane, District 2, Ho Chi Minh City'),
('Sophia Kim', 'sophia.kim@email.com', 'sophia654', 'reader', 'active', 978901234, '222 Banana Street, District 7, Ho Chi Minh City'),
('Daniel Garcia', 'daniel.garcia@email.com', 'daniel987', 'reader', 'block', 989012345, '333 Coconut Road, District 4, Ho Chi Minh City'),
('Olivia Lopez', 'olivia.lopez@email.com', 'olivia852', 'reader', 'active', 990123456, '444 Papaya Avenue, District 9, Ho Chi Minh City'),
('William Hernandez', 'william.hernandez@email.com', 'william159', 'reader', 'active', 901234890, '555 Mango Street, District 6, Ho Chi Minh City'),
('Isabella Clark', 'isabella.clark@email.com', 'isabella753', 'reader', 'active', 912345901, '666 Pineapple Road, District 8, Ho Chi Minh City'),
('Ethan Nguyen', 'ethan.nguyen@email.com', 'ethan111', 'reader', 'active', 913456789, '12 Le Loi Street, District 1, Ho Chi Minh City'),
('Liam Tran', 'liam.tran@email.com', 'liam222', 'reader', 'active', 924567890, '98 Nguyen Hue Boulevard, District 1, Ho Chi Minh City'),
('Ava Pham', 'ava.pham@email.com', 'ava333', 'reader', 'block', 935678901, '45 Vo Van Tan Street, District 3, Ho Chi Minh City'),
('Noah Hoang', 'noah.hoang@email.com', 'noah444', 'reader', 'active', 946789012, '76 Cach Mang Thang 8, District 10, Ho Chi Minh City'),
('Mia Le', 'mia.le@email.com', 'mia555', 'reader', 'active', 957890123, '21 Phan Xich Long, Phu Nhuan District, Ho Chi Minh City');

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
('Being Digital', 4, 'Alfred A. Knopf, Inc', 1995, 'A classic tech book exploring how digital technology reshapes all parts of life — from bits vs. atoms to the digital future of media, communication, and society. It''s less about code and more about the philosophy of living in a digital world', 'Nicholas Negroponte', '../../public/images/Technology/tech1.jpg'),
('Life 3.0: Being Human in the Age of Artificial Intelligence', 4, 'Alfred A. Knopf (US); Allen Lane (UK)', 2017, 'A deep-dive into the future of AI — how it works, potential impacts on society, jobs, ethics, and the pathways toward positive (or risky) futures with intelligent machines', 'Max Tegmark', '../../public/images/Technology/tech2.jpg'),
('Revolution in the Valley: The Insanely Great Story of How the Mac Was Made', 4, 'O''Reilly Media', 2004, 'Insider recount of how the Apple Macintosh was developed — a mix of technical storytelling and personal anecdotes from an original member of the Mac team. Cool for history/tech culture nerds', 'Andy Hertzfeld', '../../public/images/Technology/tech3.jpg'),
('A Brief History of Time', 2, 'Bantam Dell Publishing Group', 1988, 'Explains the universe, time, black holes, and the Big Bang in an accessible way for general readers. One of the most famous popular science books of the 20th century', 'Stephen Hawking', '../../public/images/Science/science1.jpg'),
('Fundamentals: Ten Keys to Reality', 2, 'Penguin Random House', 2021, 'Nobel laureate Frank Wilczek presents ten fundamental principles that underlie reality, from space and time to the structure of matter, in an accessible way for curious minds', 'Frank Wilczek', '../../public/images/Science/science2.jpg'),
('Physics of the Future', 2, 'Vintage / Doubleday', 2011, 'Predicts how science and tech like AI, space travel, and medicine might evolve by the year 2100, based on interviews with top scientists', 'Michio Kaku', '../../public/images/Science/science3.jpg'),
('How to Win Friends and Influence People', 6, 'Simon & Schuster', 1936, 'A timeless classic on interpersonal skills, leadership, and influence in business and life. Teaches how to connect with people effectively and build better relationships — core for networking, sales, and team leadership', 'Dale Carnegie', '../../public/images/Business/business1.jpg'),
('Shoe Dog: A Memoir by the Creator of Nike', 6, 'Simon & Schuster / Scribner', 2016, 'A candid memoir from Nike''s co‑founder documenting the messy, chaotic road to building one of the world''s most iconic brands. It highlights persistence, entrepreneurship, and risk in the real business world', 'Phil Knight', '../../public/images/Business/business2.jpg'),
('The Seven-Day Weekend', 6, 'Century', 2003, 'Challenges traditional work models by advocating for flexible, self-managed workplaces. Offers a radical perspective on work culture and innovation', 'Ricardo Semler', '../../public/images/Business/business3.jpg'),
('The Science of Success', 6, 'John Wiley & Sons', 2007, 'Explains Market‑Based Management (MBM) principles used to grow Koch Industries into a global powerhouse. Focuses on decision‑making, incentives, and organizational value creation', 'Charles G. Koch', '../../public/images/Business/business4.jpg');

INSERT INTO Book_Items (book_id, status, barcode) VALUES
(1, 'available', 1001001),
(1, 'borrowed', 1001002),
(1, 'available', 1001003),
(2, 'borrowed', 1002001),
(2, 'available', 1002002),
(3, 'available', 1003001),
(3, 'available', 1003002),
(3, 'borrowed', 1003003),
(4, 'available', 1004001),
(4, 'available', 1004002),
(5, 'borrowed', 1005001),
(5, 'available', 1005002),
(5, 'available', 1005003),
(6, 'available', 1006001),
(6, 'damaged', 1006002),
(7, 'borrowed', 1007001),
(7, 'available', 1007002),
(7, 'available', 1007003),
(8, 'available', 1008001),
(8, 'available', 1008002),
(9, 'lost', 1009001),
(9, 'available', 1009002),
(10, 'available', 1010001),
(10, 'borrowed', 1010002);


INSERT INTO Loans (user_id, book_items_id, borrow_date, due_date, return_date, status) VALUES
(2, 1, '2025-12-01', '2025-12-15', '2025-12-14', 'returned'),
(3, 3, '2025-12-05', '2025-12-19', '2025-12-18', 'returned'),
(2, 7, '2025-11-15', '2025-11-29', '2025-11-28', 'returned'),
(3, 9, '2025-11-20', '2025-12-04', '2025-12-03', 'returned'),
(4, 13, '2025-11-25', '2025-12-09', '2025-12-08', 'returned'),
(6, 16, '2025-12-01', '2025-12-15', '2025-12-14', 'returned'),
(2, 2, '2026-01-05', '2026-01-19', NULL, 'borrowing'),
(3, 4, '2026-01-06', '2026-01-20', NULL, 'borrowing'),
(4, 8, '2026-01-07', '2026-01-21', NULL, 'borrowing'),
(6, 11, '2026-01-08', '2026-01-22', NULL, 'borrowing'),
(3, 23, '2026-01-09', '2026-01-23', NULL, 'borrowing'),
(2, 15, '2025-12-20', '2026-01-03', NULL, 'overdue'),
(4, 19, '2025-12-25', '2026-01-08', NULL, 'overdue'),
(3, 6, '2025-12-28', '2026-01-11', NULL, 'renewal');


/* =========================
   VERIFICATION QUERIES
   ========================= */

SELECT 'Users' as Table_Name, COUNT(*) as Total FROM Users
UNION ALL
SELECT 'Categories', COUNT(*) FROM Categories
UNION ALL
SELECT 'Books', COUNT(*) FROM Books
UNION ALL
SELECT 'Book_Items', COUNT(*) FROM Book_Items
UNION ALL
SELECT 'Loans', COUNT(*) FROM Loans
UNION ALL
SELECT 'Cart', COUNT(*) FROM Cart;

SELECT 
    b.book_id,
    b.title,
    COUNT(bi.book_items_id) as total_copies,
    SUM(CASE WHEN bi.status = 'available' THEN 1 ELSE 0 END) as available,
    SUM(CASE WHEN bi.status = 'borrowed' THEN 1 ELSE 0 END) as borrowed,
    SUM(CASE WHEN bi.status = 'damaged' THEN 1 ELSE 0 END) as damaged,
    SUM(CASE WHEN bi.status = 'lost' THEN 1 ELSE 0 END) as lost
FROM Books b
LEFT JOIN Book_Items bi ON b.book_id = bi.book_id
GROUP BY b.book_id, b.title
ORDER BY b.book_id;

SELECT 
    status,
    COUNT(*) as total_loans
FROM Loans
GROUP BY status
ORDER BY status;