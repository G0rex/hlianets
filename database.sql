create table news
(
    id                int auto_increment primary key,
    title             varchar(255)                          not null,
    short_description text                                  not null,
    content           text                                  not null,
    created_at        timestamp default current_timestamp() not null
);

