-- Active: 1730390521538@@127.0.0.1@3306@KBN_database
CREATE DATABASE KBN_database;
USE KBN_database;

-- Students Table
CREATE TABLE Students (
    sid INT PRIMARY KEY,
    sname VARCHAR(50) NOT NULL,
    age INT NOT NULL
);
INSERT INTO Students VALUES
(100, 'Kelly', 20),
(101, 'Brandon', 21),
(102, 'Nayam', 22),
(103, 'Jane', 21),
(104, 'Bob', 20);

-- Courses Table
CREATE TABLE Courses (
    cid INT PRIMARY KEY,
    cname VARCHAR(50),
    credits INT
);
INSERT INTO Courses VALUES
(212, 'Data Structures', 4),
(213, 'Computer Networks', 3),
(214, 'Python', 2),
(215, 'Java', 2),
(313, 'Computer Languages', 2),
(315, 'Software Development', 4),
(355, 'Database Systems', 4),
(379, 'CS Theory', 2),
(415, 'Engineering', 4);

ALTER TABLE Courses ADD slots INT DEFAULT 25;

-- Prerequisites Table
CREATE TABLE Prerequisites (
    cid INT,
    prereq_cid INT,
    PRIMARY KEY (cid, prereq_cid),
    FOREIGN KEY (cid) REFERENCES Courses(cid),
    FOREIGN KEY (prereq_cid) REFERENCES Courses(cid)
);
INSERT INTO Prerequisites VALUES
(313, 212),
(315, 213),
(355, 214),
(379, 215),
(415, 379);

-- Schedule Table
CREATE TABLE Schedule (
    cid INT PRIMARY KEY,
    class_time VARCHAR(20),
    building VARCHAR(50),
    FOREIGN KEY (cid) REFERENCES Courses(cid)
);
INSERT INTO Schedule VALUES
(355, 'M 08:15:00', 'Computer Science'),
(315, 'TU 10:30:00', 'Business'),
(415, 'W 10:45:00', 'Susan Cole Hall'),
(379, 'FR 11:15:00', 'Dickson Hall'),
(313, 'M 09:30:00', 'Business'),
(212, 'TU 12:30:00', 'Business'),
(213, 'W 13:45:00', 'Susan Cole Hall'),
(214, 'FR 08:45:00', 'Richardson Hall'),
(215, 'FR 08:45:00', 'Dickson Hall');

-- Professor Table
CREATE TABLE Professor (
    pid INT PRIMARY KEY,
    pname VARCHAR(50),
    department VARCHAR(50),
    email VARCHAR(100)
);
INSERT INTO Professor VALUES
(200, 'Hao', 'Computer Science', 'lhao@montclair.edu'),
(201, 'Samuels', 'Business', 'rsamuels@montclair.edu'),
(202, 'Kazi', 'Environmental Science', 'hkazi@montclair.edu'),
(203, 'Jenq', 'Dickson Hall', 'jjenq@montclair.edu'),
(204, 'Coutras', 'Richardson Hall', 'mcoutras@montclair.edu'),
(205, 'Kong', 'Susan Cole Hall', 'ykong@montclair.edu');

-- Teaching Table
CREATE TABLE Teaching (
    pid INT,
    cid INT,
    PRIMARY KEY (pid, cid),
    FOREIGN KEY (pid) REFERENCES Professor(pid),
    FOREIGN KEY (cid) REFERENCES Courses(cid)
);
INSERT INTO Teaching VALUES
(200, 355),
(201, 315),
(201, 313),
(203, 379),
(204, 313),
(205, 355),
(205, 415),
(205, 313),
(205, 379);

-- Enrollment Table
CREATE TABLE Enrollment (
    sid INT,
    cid INT,
    grade INT,
    PRIMARY KEY (sid, cid),
    FOREIGN KEY (sid) REFERENCES Students(sid),
    FOREIGN KEY (cid) REFERENCES Courses(cid)
);
INSERT INTO Enrollment VALUES
(100, 315, 92),
(100, 355, 100),
(101, 415, 88),
(101, 313, 93),
(102, 355, 100),
(102, 313, 71),
(103, 415, 89),
(103, 313, 100),
(104, 355, 68),
(104, 313, 74);