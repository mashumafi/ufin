SET FOREIGN_KEY_CHECKS = 0;

drop table if exists account, oauth, status, course, role, registration, assignment, accountsInAssignment;

SET FOREIGN_KEY_CHECKS = 1;
SET storage_engine=InnoDB;

CREATE TABLE role (
	id integer not null auto_increment,
	description varchar(20) not null,
	primary key(id)
); 

CREATE TABLE account (
	id integer not null auto_increment,
	roleId integer,
	firstName varchar(20) not null,
	lastName varchar(20) not null,
	accessToken varchar(60),
	refreshToken varchar(60),
	expires long,
	email varchar(64) null,
	primary key(id),
	foreign key (roleId) references role (id) on delete cascade on update cascade
);
CREATE UNIQUE INDEX accountEmail ON account(email);

CREATE TABLE oauth (
	email varchar(64) not null,
	accountId integer not null,
	primary key(email, accountId),
	foreign key (accountId) references account (id) on delete cascade on update cascade
);

CREATE TABLE status (
	accountId integer not null,
	code integer not null,
	at datetime not null,
	foreign key (accountId) references account (id) on delete cascade on update cascade
);

CREATE TABLE course (
	id integer not null auto_increment,
	name varchar(20) not null,
	referenceNumber VARCHAR(32) not null,
	primary key(id)
);

CREATE TABLE registration (
	accountId integer not null,
	courseId integer not null,
	roleId integer not null,
	registered datetime not null,
	primary key(accountId, courseId),
	foreign key (accountId) references account (id) on delete cascade on update cascade,
	foreign key (courseId) references course (id) on delete cascade on update cascade,
	foreign key (roleId) references role (id) on delete cascade on update cascade
);

CREATE TABLE assignment (
	id integer not null auto_increment,
	authorId integer not null,
	courseId integer not null,
	title varchar(20) not null,
	docId varchar(60) not null,
	maxGrade integer not null,
	issued datetime not null,
	due datetime not null,
	primary key(id),
	foreign key (authorId) references account (id) on delete cascade on update cascade,
	foreign key (courseId) references course (id) on delete cascade on update cascade
);

CREATE TABLE accountsInAssignment (
	accountId integer not null,
	courseId integer not null,
	assignmentId integer not null,
	docId varchar(60) not null,
	grade integer not null,
	submitted datetime null,
	primary key(accountId, courseId, assignmentId),
	foreign key (accountId) references account (id) on delete cascade on update cascade,
	foreign key (courseId) references assignment (courseId) on delete cascade on update cascade,
	foreign key (assignmentId) references assignment (id) on delete cascade on update cascade
);

SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO role (description) VALUES ("Administrator");
INSERT INTO role (description) VALUES ("Instructor");
INSERT INTO role (description) VALUES ("Assistant");
INSERT INTO role (description) VALUES ("Student");

INSERT INTO account (email, firstName, lastName, roleId) VALUES ("shi@temple.edu", "Justin", "Shi", 1);
INSERT INTO account (email, firstName, lastName, roleId) VALUES ("michael.boldin@temple.edu", "Michael", "Boldin", 1);
INSERT INTO account (email, firstName, lastName, roleId) VALUES ("mashumafi@temple.edu", "Matthew", "Murphy", 1);
INSERT INTO account (email, firstName, lastName, roleId) VALUES ("tuc51123@temple.edu", "Matthew", "Murphy", 4);
INSERT INTO account (email, firstName, lastName, roleId) VALUES ("tuc51727@temple.edu", "Logan", "Murphy", 4);
INSERT INTO account (email, firstName, lastName, roleId) VALUES ("dboldin@temple.edu", "David", "Boldin", 1);

INSERT INTO oauth (email, accountId) VALUES ("shi@temple.edu", 1);
INSERT INTO oauth (email, accountId) VALUES ("michael.boldin@temple.edu", 2);
INSERT INTO oauth (email, accountId) VALUES ("mboldin@temple.edu", 2);
INSERT INTO oauth (email, accountId) VALUES ("mashumafi@gmail.com", 3);
INSERT INTO oauth (email, accountId) VALUES ("tuc51123@temple.edu", 4);
INSERT INTO oauth (email, accountId) VALUES ("tuc51727@temple.edu", 5);
INSERT INTO oauth (email, accountId) VALUES ("dboldin@temple.edu", 6);

SET FOREIGN_KEY_CHECKS = 1;