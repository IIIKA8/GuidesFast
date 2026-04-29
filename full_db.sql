-- Full DB script for shop_exam demo
-- Import in MySQL Workbench or mysql CLI.

CREATE DATABASE IF NOT EXISTS `shop_exam`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `shop_exam`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `userlist`;
DROP TABLE IF EXISTS `shift`;
DROP TABLE IF EXISTS `order`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `userrole`;

CREATE TABLE `userrole` (
  `userroleid` INT NOT NULL AUTO_INCREMENT,
  `namerole` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`userroleid`),
  UNIQUE KEY `uq_userrole_namerole` (`namerole`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user` (
  `userid` INT NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(30) NOT NULL DEFAULT 'Работает',
  `lastname` VARCHAR(100) NOT NULL,
  `firstname` VARCHAR(100) NOT NULL,
  `middlename` VARCHAR(100) NOT NULL,
  `login` VARCHAR(64) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `userroleid` INT NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `uq_user_login` (`login`),
  KEY `idx_user_userroleid` (`userroleid`),
  CONSTRAINT `fk_user_role`
    FOREIGN KEY (`userroleid`) REFERENCES `userrole` (`userroleid`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order` (
  `orderid` INT NOT NULL AUTO_INCREMENT,
  `orderstatus` VARCHAR(50) NOT NULL DEFAULT 'создан',
  `roomnumber` VARCHAR(50) NOT NULL,
  `amountclients` INT NOT NULL,
  `hotelservices` VARCHAR(255) NOT NULL,
  `paymentstatus` VARCHAR(50) NOT NULL DEFAULT 'не принят',
  `datecreation` DATE NOT NULL,
  PRIMARY KEY (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `shift` (
  `shiftid` INT NOT NULL AUTO_INCREMENT,
  `datestart` DATE NOT NULL,
  `dateend` DATE NOT NULL,
  PRIMARY KEY (`shiftid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `userlist` (
  `userid` INT NOT NULL,
  `shiftid` INT NOT NULL,
  PRIMARY KEY (`userid`, `shiftid`),
  KEY `idx_userlist_shiftid` (`shiftid`),
  CONSTRAINT `fk_userlist_user`
    FOREIGN KEY (`userid`) REFERENCES `user` (`userid`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_userlist_shift`
    FOREIGN KEY (`shiftid`) REFERENCES `shift` (`shiftid`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `userrole` (`userroleid`, `namerole`) VALUES
  (1, 'Администратор'),
  (2, 'Менеджер'),
  (3, 'Исполнитель');

INSERT INTO `user` (`userid`, `status`, `lastname`, `firstname`, `middlename`, `login`, `password`, `userroleid`) VALUES
  (1, 'Работает', 'Иванов', 'Иван', 'Иванович', 'ida', '2045', 1),
  (2, 'Работает', 'Петров', 'Лев', 'Олегович', 'leh', '1234', 2),
  (3, 'Работает', 'Сидоров', 'Влад', 'Тимофеевич', 'vlt', '2707', 3);

INSERT INTO `shift` (`shiftid`, `datestart`, `dateend`) VALUES
  (1, '2026-04-01', '2026-04-07'),
  (2, '2026-04-08', '2026-04-14');

INSERT INTO `userlist` (`userid`, `shiftid`) VALUES
  (1, 1),
  (2, 1),
  (3, 2);

INSERT INTO `order` (`orderid`, `orderstatus`, `roomnumber`, `amountclients`, `hotelservices`, `paymentstatus`, `datecreation`) VALUES
  (1, 'принят', '101', 2, 'Завтрак', 'принят', '2026-04-10'),
  (2, 'не принят', '203', 1, 'Без услуг', 'не принят', '2026-04-11'),
  (3, 'готовится', '305', 3, 'Трансфер', 'принят', '2026-04-12');

SET FOREIGN_KEY_CHECKS = 1;
