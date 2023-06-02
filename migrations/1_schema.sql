create table if not exists stations (
    id_station   varchar not null primary key,
    name         varchar not null,
    lat          float not null,
    lng          float not null
);
