/*==============================================================*/
/* Table: "USER"                                                */
/*==============================================================*/
create table user_profile
(
    USER_ID    SERIAL PRIMARY KEY,
    LAST_NAME  VARCHAR(255)        not null,
    FIRST_NAME VARCHAR(255)        not null,
    PATRONYMIC VARCHAR(255)        null,
    EMAIL      VARCHAR(255) UNIQUE not null,
    PASSWORD   VARCHAR(255)        not null
);

/*==============================================================*/
/* Index: USER_PK                                               */
/*==============================================================*/
create unique index USER_PK on user_profile (USER_ID);

/*==============================================================*/
/* Table: POST                                                  */
/*==============================================================*/
create table POST
(
    POST_ID          SERIAL primary key,
    USER_ID          integer references user_profile (USER_ID) not null,
    POST_NAME        VARCHAR(255)                              not null,
    POST_CREATE_DATE TIMESTAMP                                 not null,
    POST_EDIT_DATE   TIMESTAMP                                 not null,
    POST_DESCRIPTION VARCHAR(5000)                             not null
);

/*==============================================================*/
/* Index: POST_PK                                               */
/*==============================================================*/
create unique index POST_PK on POST (POST_ID);

/*==============================================================*/
/* Table: FILE                                                  */
/*==============================================================*/
create table FILE
(
    FILE_ID             SERIAL primary key,
    POST_ID             integer references POST (POST_ID) not null,
    FILE_NAME           VARCHAR(50)                       not null,
    FILE_TYPE           VARCHAR(50)                       not null,
    FILE_PATH           VARCHAR(120)                      not null,
    FILE_SRC_NAME       VARCHAR(50)                       not null,
    FILE_DOWNLOAD_COUNT integer                           not null default 0
);

/*==============================================================*/
/* Index: FILE_PK                                               */
/*==============================================================*/
create unique index FILE_PK on FILE (FILE_ID);

/*==============================================================*/
/* Index: INCLUDE_FK                                            */
/*==============================================================*/
create index INCLUDE_FK on FILE (POST_ID);

/*==============================================================*/
/* Index: CREATE_FK                                             */
/*==============================================================*/
create index CREATE_FK on POST (USER_ID);

alter table FILE
    add constraint FK_FILE_INCLUDE_POST foreign key (POST_ID)
        references POST (POST_ID)
        on delete cascade on update cascade;

alter table POST
    add constraint FK_POST_CREATE_USER foreign key (USER_ID)
        references user_profile (USER_ID)
        on delete restrict on update cascade;