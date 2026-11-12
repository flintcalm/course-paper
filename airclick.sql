CREATE DATABASE IF NOT EXISTS airclick CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE airclick;

CREATE TABLE users (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    login       VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    surname     VARCHAR(100) NOT NULL,
    name        VARCHAR(100) NOT NULL,
    patronymic  VARCHAR(100) NULL,
    phone       VARCHAR(15)  NOT NULL,
    email       VARCHAR(255) NOT NULL UNIQUE,
    gender      ENUM('male','female') NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_login (login),
    INDEX idx_email (email)
);

CREATE TABLE fare_classes (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    code        ENUM('economy','business','first') NOT NULL UNIQUE,
    name        VARCHAR(50) NOT NULL,
    coefficient DECIMAL(4,2) NOT NULL DEFAULT 1.00
);

INSERT INTO fare_classes (code, name, coefficient) VALUES
('economy',  'Эконом',         1.00),
('business', 'Бизнес',         2.00),
('first',    'Первый класс',   3.00);

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
    stops            VARCHAR(100)   NULL,
    INDEX idx_from  (from_city),
    INDEX idx_to    (to_city),
    INDEX idx_price (price)
);

CREATE TABLE bookings (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    code            VARCHAR(20)   NOT NULL UNIQUE,
    user_id         INT           NOT NULL,
    flight_id       INT           NOT NULL,
    class           ENUM('economy','business','first') NOT NULL DEFAULT 'economy',
    departure_date  DATE          NOT NULL,
    email           VARCHAR(255)  NOT NULL,
    phone           VARCHAR(50)   NOT NULL,
    status          ENUM('active','confirmed','cancelled','completed') NOT NULL DEFAULT 'active',
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_bookings_user  (user_id),
    INDEX idx_bookings_email (email),
    INDEX idx_bookings_code  (code)
);

CREATE TABLE passengers (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    booking_id     INT          NOT NULL,
    surname        VARCHAR(100) NOT NULL,
    name           VARCHAR(100) NOT NULL,
    patronymic     VARCHAR(100) NULL,
    birth_date     DATE         NOT NULL,
    gender         ENUM('male','female') NOT NULL,
    document       VARCHAR(30)  NOT NULL,
    INDEX idx_passengers_booking (booking_id)
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

-- Связь бронирования с пользователем
ALTER TABLE bookings
ADD CONSTRAINT fk_bookings_user
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE RESTRICT ON UPDATE CASCADE;

-- Связь бронирования с рейсом
ALTER TABLE bookings
ADD CONSTRAINT fk_bookings_flight
FOREIGN KEY (flight_id) REFERENCES flights(id)
ON DELETE RESTRICT ON UPDATE CASCADE;

-- Связь пассажира с бронированием
ALTER TABLE passengers
ADD CONSTRAINT fk_passengers_booking
FOREIGN KEY (booking_id) REFERENCES bookings(id)
ON DELETE CASCADE ON UPDATE CASCADE;