CREATE TABLE Account (
    Id INT AUTO_INCREMENT  PRIMARY KEY ,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    PhoneNb VARCHAR(20) NOT NULL,
    Address TEXT NOT NULL,
    Location VARCHAR(100) NOT NULL,
    Age INT NOT NULL,
    Gmail VARCHAR(100) unique  NOT NULL,
    Password VARCHAR(255) NOT NULL,
    AccountType ENUM('Client', 'Employee', 'Admin', 'DeliveryMan') NOT NULL );
    
-- Client table (inherits from Account)
CREATE TABLE Client (
    AccountId INT PRIMARY KEY,
    Points INT DEFAULT 0,
    FOREIGN KEY (AccountId) REFERENCES Account(Id) ON DELETE CASCADE
);

-- Employee table (inherits from Account)
CREATE TABLE Employee (
    AccountId INT  PRIMARY KEY,
    Salary DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (AccountId) REFERENCES Account(Id) ON DELETE CASCADE
);

-- Admin table (inherits from Employee)
CREATE TABLE Admin (
    AccountId INT PRIMARY KEY,
    FOREIGN KEY (AccountId) REFERENCES Employee(AccountId) ON DELETE CASCADE
);

-- DeliveryMan table (inherits from Employee)
CREATE TABLE DeliveryMan (
    AccountId INT PRIMARY KEY,
    FOREIGN KEY (AccountId) REFERENCES Employee(AccountId) ON DELETE CASCADE
);

-- Categorie table
CREATE TABLE Categorie (
    Id INT PRIMARY KEY,
    Name VARCHAR(50) NOT NULL
);

-- Product table
CREATE TABLE Product (
    Id INT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    Description TEXT,
    CategorieId VARCHAR(36) NOT NULL,
    FOREIGN KEY (CategorieId) REFERENCES Categorie(Id) ON DELETE CASCADE
);

-- Order table
CREATE TABLE `Order` (
    Id INT PRIMARY KEY,
    Status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') NOT NULL,
    Description TEXT,
    Date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Total DECIMAL(10,2) NOT NULL,
    ClientId INT NOT NULL,
    DeliveryManId INT,
    FOREIGN KEY (ClientId) REFERENCES Client(AccountId),
    FOREIGN KEY (DeliveryManId) REFERENCES DeliveryMan(AccountId) ON DELETE CASCADE
);

-- OrderProduct (junction table for many-to-many relationship)
CREATE TABLE OrderProduct (
    OrderId INT NOT NULL,
    ProductId int NOT NULL,
    Quantity INT NOT NULL DEFAULT 1,
    PRIMARY KEY (OrderId, ProductId),
    FOREIGN KEY (OrderId) REFERENCES `Order`(Id) ON DELETE CASCADE,
    FOREIGN KEY (ProductId) REFERENCES Product(Id) ON DELETE CASCADE
);

INSERT INTO Categorie (Id, Name) VALUES
    (1, 'Pasta'),
    (2, 'Drinks'),
    (3, 'Desserts'),
    (4, 'Pizza'),
    (5, 'Burgers');

INSERT INTO Product (Id, Name, Price, Description, CategorieId) VALUES
    -- Pasta (id 1)
    (1, 'Spaghetti Carbonara', 9.99, 'Classic Italian pasta with creamy sauce, bacon, and cheese.', 1),
    (2, 'Penne Arrabbiata', 8.49, 'Penne pasta in spicy tomato sauce.', 1),

    -- Drinks (id 2)
    (3, 'Coca-Cola', 1.99, 'Refreshing cold drink.', 2),
    (4, 'Orange Juice', 2.49, 'Freshly squeezed orange juice.', 2),

    -- Desserts (id 3)
    (5, 'Chocolate Cake', 4.99, 'Rich and moist chocolate cake.', 3),
    (6, 'Tiramisu', 5.49, 'Classic Italian dessert with coffee and mascarpone.', 3),

     -- Pizza (id 4)
    (7, 'Margherita Pizza', 10.99, 'Tomato, mozzarella, and basil.', 4),
    (8, 'Pepperoni Pizza', 12.49, 'Classic pizza with pepperoni slices.', 4),

    -- Burgers (id 5)
    (9, 'Classic Burger', 8.99, 'Juicy beef patty with lettuce, tomato, and cheese.', 5),
    (10, 'Chicken Burger', 7.99, 'Grilled chicken breast with fresh veggies.', 5);

-- Admin Secret Key table
CREATE TABLE AdminSecretKey (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    SecretKey VARCHAR(255) NOT NULL UNIQUE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--  NEW TABLES FOR RESERVATIONS
CREATE TABLE reservations (
  id int NOT NULL AUTO_INCREMENT,
  date date DEFAULT NULL,
  time time DEFAULT NULL,
  party_size int DEFAULT NULL,
  client_id int DEFAULT NULL,
  PRIMARY KEY (id),
  KEY client_id (client_id),
  CONSTRAINT reservations_ibfk_1 FOREIGN KEY (client_id) REFERENCES client (AccountId) ON DELETE CASCADE
);

CREATE TABLE contact_messages (
  id int NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  subject varchar(200) NOT NULL,
  message text NOT NULL,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);