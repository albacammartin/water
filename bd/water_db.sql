DROP DATABASE IF EXISTS water_db;
CREATE DATABASE water_db CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
USE water_db;
CREATE TABLE users_data (
    idUser INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    telefono VARCHAR(15) NOT NULL,
    fechaNacimiento DATE NOT NULL,
    direccion VARCHAR(100),
    sexo ENUM('MUJER', 'HOMBRE', 'OTRO')
);

CREATE TABLE users_login(
    idLogin INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    idUser INT NOT NULL UNIQUE,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'user') NOT NULL,
    FOREIGN KEY (idUser) REFERENCES users_data(idUser) ON DELETE CASCADE
);

CREATE TABLE citas(
    idCita INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    idUser INT NOT NULL,
    fechaCita DATE NOT NULL,
    motivoCita VARCHAR(255),
    FOREIGN KEY (idUser) REFERENCES users_data(idUser) ON DELETE CASCADE
);

CREATE TABLE noticias(
    idNoticia INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    idUser INT NOT NULL,
    titulo VARCHAR(100) NOT NULL UNIQUE,
    imagen VARCHAR(255) NOT NULL,
    texto LONGTEXT NOT NULL,
    fecha DATE NOT NULL,
    FOREIGN KEY (idUser) REFERENCES users_data(idUser) ON DELETE RESTRICT
);

-- DATOS DE PRUEBA
INSERT INTO users_data (nombre, apellidos, email, telefono, fechaNacimiento, direccion, sexo) VALUES ('Alba', 'Camacho Martín', 'Alba@gmail.com', '600223344','2001-11-07', 'Sevilla', 'MUJER');

-- INSERTS NOTICIAS
INSERT INTO noticias (idUser, titulo, imagen, texto, fecha) VALUES (1, 'Abrimos las citas de diagnóstico de piel', 'img/noticias/citas-diagnostico.jpg', 'Desde este mes puedes reservar una cita gratuita con nuestras especialistas. En unos 30 minutos analizamos tu tipo de piel, su nivel de hidratación y tus hábitos para recomendarte una rutina a tu medida.\n\nSolo tienes que registrarte en la web y entrar en la sección Citaciones para elegir el día que mejor te venga.', '2026-09-28'),
(1, 'Así es nuestra rutina facial en 4 pasos', 'img/noticias/rutina-facial.jpg', 'Limpiar, tratar, hidratar y refrescar. Con el Aqua Cleanser eliminas impurezas sin resecar, el Hydra Serum aporta ácido hialurónico, la Aqua Gel Cream sella la hidratación y el Hydra Mist te acompaña durante el día.\n\nUna rutina ligera pensada para pieles que no quieren sentir peso ni brillos.', '2026-09-14'),
(1, 'Llega la nueva línea de cuidado capilar', 'img/noticias/linea-capilar.jpg', 'Hydra Shampoo, Silk Conditioner, Leave-in Milk, Wave Mist, Scalp Serum y Repair Hair Mask: seis fórmulas inspiradas en el agua para un cabello hidratado, suave y con movimiento.\n\nTodas comparten ingredientes como el aloe vera, la biotina y el agua de coco.', '2026-08-30'),
(1, 'Maquillaje ligero: base, máscara y polvos', 'img/noticias/maquillaje-ligero.jpg', 'Nuestra Weightless Foundation con SPF 20 unifica el tono sin cubrir la piel, la Lash Flow Mascara define sin apelmazar y los Translucent Setting Powder fijan el maquillaje con acabado natural.\n\nTres básicos para un look fresco de la mañana a la noche.', '2026-08-12');

