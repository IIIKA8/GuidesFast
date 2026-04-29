CREATE USER IF NOT EXISTS 'lehh'@'localhost' IDENTIFIED BY 'lehh123_!';
GRANT ALL PRIVILEGES ON shop_exam.* TO 'lehh'@'localhost';
FLUSH PRIVILEGES;