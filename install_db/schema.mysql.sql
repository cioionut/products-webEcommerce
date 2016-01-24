create table if not exists currencies (
    id int(10) primary key auto_increment,
    code varchar(3) not null unique,
    rate float(10,4) not null,
    def boolean not null default false,
    active boolean not null default true
);

create table if not exists users (
    id int(10) primary key auto_increment,
    email varchar(32) not null unique,
    password varchar(32) not null,
    currency_id int(10),
    hash varchar(32) not null,
    verified boolean default false not null,
    foreign key (currency_id) references currencies(id) on delete set null on update cascade
);

create table if not exists admins (
    id int(10) primary key auto_increment,
    user_id int(10) unique not null,
    foreign key (user_id) references users(id)
    on delete cascade
    on update cascade
);

create table if not exists categories(
    id int(10) primary key auto_increment,
    name varchar(256)
);

create table if not exists products(
    id int(10) primary key auto_increment,
    name varchar(256) not null,
    category_id int(10),
    price float(10,2) not null,
    file varchar(256),
    image varchar(256),
    description text,
    foreign key (category_id) REFERENCES categories(id) on delete set null on update cascade
);

create table if not exists mailsettings (
    id int(10) primary key auto_increment,
    smtp_config mediumtext,
    default_config boolean default false
);

create table if not exists shoppingcarts (
    id int(10) primary key auto_increment,
    user_id int(10),
    product_id int(10),
    foreign key (user_id) references users(id) on delete cascade on update cascade,
    foreign key (product_id) references products(id) on delete cascade on update cascade,
    quantity int(10) DEFAULT 0,
    constraint unique_entry unique (user_id, product_id)
);

create table if not exists orders (
    id int(10) primary key auto_increment,
    transaction_id  varchar(32) unique,
    user_id int(10),
    state varchar(32) not null,
    email varchar(32) not null,
    create_date timestamp default current_timestamp,
    foreign key (user_id) references users(id) on delete set null on update cascade
);

create table if not exists ordered_products (
    id int(10) primary key auto_increment,
    order_id int(10),
    product_id int(10),
    name varchar(256) not null,
    category_id int(10),
    price float(10,2) not null,
    currency varchar(3) not null,
    quantity int(10) not null,
    foreign key (category_id) REFERENCES categories(id) on delete set null on update cascade,
    foreign key (order_id) references orders(id) on delete cascade on update cascade,
    foreign key (product_id) references products(id) on delete set null on update cascade
);



insert into currencies values (1, 'USD', 1, 1, 1);
insert into users values (1, 'admin@products-pilot.loc','21232f297a57a5a743894a0e4a801fc3', 1, 'superuser', 1);
insert into admins(user_id) values (1);
insert into categories values (1, 'Hardware'),(2, 'Software'),(3, 'Book'),(4, 'Movie');
