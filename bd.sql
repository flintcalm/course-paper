CREATE DATABASE IF NOT EXISTS airclick CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE airclick;

CREATE TABLE flights (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    from_city        VARCHAR(100)   NOT NULL,
    to_city          VARCHAR(100)   NOT NULL,
    departure_time   CHAR(5)        NOT NULL,
    arrival_time     CHAR(5)        NOT NULL,
    duration         VARCHAR(20)    NOT NULL,
    duration_min     INT            NOT NULL,
    airline          VARCHAR(100)   NOT NULL,
    flight_number    VARCHAR(20)    NOT NULL,
    aircraft         VARCHAR(50)    NOT NULL,
    price            DECIMAL(10,2)  NOT NULL,
    currency         VARCHAR(5)     NOT NULL DEFAULT '₽',
    stops            VARCHAR(100)   NOT NULL,
    arrival_next_day TINYINT(1)     NOT NULL DEFAULT 0,
    INDEX idx_from (from_city),
    INDEX idx_to   (to_city),
    INDEX idx_price (price)
);

CREATE TABLE bookings (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    code        VARCHAR(20)  NOT NULL UNIQUE,
    flight_id   INT          NOT NULL,
    email       VARCHAR(255) NOT NULL,
    phone       VARCHAR(50)  NOT NULL,
    status      ENUM('active','confirmed','cancelled','completed') NOT NULL DEFAULT 'active',
    total_price DECIMAL(10,2) NOT NULL,
    currency    VARCHAR(5)    NOT NULL DEFAULT '₽',
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flight_id) REFERENCES flights(id),
    INDEX idx_email (email)
);

CREATE TABLE passengers (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    booking_id  INT          NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    first_name  VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) NULL,
    birth_date  DATE         NOT NULL,
    gender      ENUM('male','female') NOT NULL,
    document    VARCHAR(50)  NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE TABLE contact_messages (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    phone      VARCHAR(50)  NULL,
    subject    VARCHAR(50)  NOT NULL,
    message    TEXT         NOT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);