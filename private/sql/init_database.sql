CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32) NOT NULL UNIQUE,
    password CHAR(255) NOT NULL,
    email VARCHAR(100),
    first_name VARCHAR(30),
    last_name VARCHAR(100),
    birth_date DATE,
    delivery_address VARCHAR(50),
    zipcode VARCHAR(10)
);

CREATE TABLE IF NOT EXISTS privileges(
    name VARCHAR(64) PRIMARY KEY
);

INSERT INTO privileges(name)
VALUES
	("category_add"),
	("category_remove"),
	("item_add"),
	("item_add_image"),
	("item_category"),
	("item_description"),
	("item_image"),
	("item_manufacturer"),
	("item_price"),
	("item_remove"),
	("item_remove_image"),
	("item_sale_absolute"),
	("item_sale_percent"),
	("item_sale_revoke"),
	("item_visible"),
	("manufacturer_add"),
	("manufacturer_remove"),
	("order_ship"),
	("root"),
	("user_add"),
	("user_grant_privilege"),
	("user_remove"),
	("user_revoke_privilege");

CREATE TABLE IF NOT EXISTS user_privileges(
    user_id INT,
    privilege VARCHAR(64),
    PRIMARY KEY (user_id, privilege),
    FOREIGN KEY (user_id)
    	REFERENCES users (id)
    	ON UPDATE RESTRICT ON DELETE RESTRICT,
    FOREIGN KEY (privilege)
    	REFERENCES privileges (name)
    	ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS manufacturers(
    name VARCHAR(64) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS categories(
    name VARCHAR(32) PRIMARY KEY,
    parent VARCHAR(32),
    FOREIGN KEY (parent)
    	REFERENCES categories (name)
    	ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS items(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(64) NOT NULL,
    manufacturer VARCHAR(64),
    description TEXT,
    date_added DATE DEFAULT CURRENT_TIMESTAMP,
    image VARCHAR(64),
    price DECIMAL(7,2) NOT NULL,
    category VARCHAR(32) NOT NULL,
    visible TINYINT DEFAULT 0,
    FOREIGN KEY (manufacturer)
    	REFERENCES manufacturers (name)
    	ON UPDATE RESTRICT ON DELETE RESTRICT,
    FOREIGN KEY (category)
    	REFERENCES categories (name)
    	ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS item_images(
    item_id INT,
    image VARCHAR(64),
    PRIMARY KEY (item_id, image),
    FOREIGN KEY (item_id)
    	REFERENCES items (id)
    	ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS sales(
    item_id INT PRIMARY KEY,
    percentage DECIMAL(4,2) NOT NULL,
    deadline TIMESTAMP NOT NULL,
  	FOREIGN KEY (item_id)
    	REFERENCES items (id)
    	ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS reviews(
    user_id INT,
    item_id INT,
    rating INT NOT NULL,
    text VARCHAR(1000),
    PRIMARY KEY (user_id, item_id),
    FOREIGN KEY (user_id)
    	REFERENCES users (id)
    	ON UPDATE RESTRICT ON DELETE RESTRICT,
  	FOREIGN KEY (item_id)
    	REFERENCES items (id)
    	ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS orders(
    id INT AUTO_INCREMENT PRIMARY KEY,
    postal_code VARCHAR(10) NOT NULL,
    delivery_address VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    time_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_products(
    order_id INT,
    item_id INT,
    quantity INT NOT NULL,
    product_price DECIMAL(7,2) NOT NULL,
    PRIMARY KEY (order_id, item_id),
    FOREIGN KEY (order_id)
    	REFERENCES orders (id)
    	ON UPDATE RESTRICT ON DELETE RESTRICT,
    FOREIGN KEY (item_id)
    	REFERENCES items (id)
    	ON UPDATE RESTRICT ON DELETE RESTRICT
);
