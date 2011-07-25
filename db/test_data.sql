INSERT INTO  `godeploy`.`users` (
`id` ,
`name` ,
`password`
)
VALUES (
1 ,  'testuser',  '$6$rounds=5000$0dce4e3873fbf395$.8RRp3yR0tF.ZCUw5mqSPFBInNJZ0CYrICy/jw/HAcdH0HvGO1GeNJiS3NKoXO7Ls.XCPWFM7UfpGvIqcpZCP.'
);

INSERT INTO  `godeploy`.`projects` (
`id` ,
`name` ,
`slug` ,
`repository_types_id` ,
`repository_url` ,
`deployment_branch` ,
`public_keys_id`
)
VALUES (
'1',  'Test Project',  'test-project',  '1',  'git@github.com:Asgrim/directory-syncing-thing.git',  'master',  '1'
);

INSERT INTO  `godeploy`.`projects` (
`id` ,
`name` ,
`slug` ,
`repository_types_id` ,
`repository_url` ,
`deployment_branch` ,
`public_keys_id`
)
VALUES (
'2',  'Another project',  'another-project',  '1',  'git@github.com:Asgrim/MAL.git',  'master',  '2'
);

INSERT INTO  `godeploy`.`public_keys` (
`id` ,
`public_key_types_id` ,
`data` ,
`comment`
)
VALUES (
'1',  '1',  'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAAAgQC47iPWpg6KeiLBR2Xt7C6LdLbJuKDL14b+aN+AqqqgPrmBoT7NadAEwOg8p6hBw8SVwqBX6UBP2q1YJCEGODefOdRCDKP7hp+0lwuUBK0nQp9n/Z4xzY//L6mor3Xe9zMz4Dm7e4UBw+9Dc+PRqYtorWPpS/HoZblyOZga+rjZ8w== test rsa key',  'test rsa key'
);

INSERT INTO  `godeploy`.`public_keys` (
`id` ,
`public_key_types_id` ,
`data` ,
`comment`
)
VALUES (
'2',  '1',  'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAAAgQDedDGG6/l6wjC44FKZ9aI6bA0S9iASLG9KbVjxvX2E9U9+dSyLalLR10PMfI4LlzNrXVlgY0Kfq4iFP31I7vo5L+8/FwTvL2aCf2rskHXDduADFVOTdXLqci5y2wmDT+rZixrpec3k7wSkJckOC/ct7iCBee3+FbZQqrnV8BdRuw== test rsa key 2',  'test rsa key 2'
);

INSERT INTO  `godeploy`.`servers` (
`id` ,
`name` ,
`hostname` ,
`connection_types_id` ,
`port` ,
`username` ,
`password` ,
`remote_path` ,
`projects_id`
)
VALUES (
1 ,  'Development server',  'dev.somesite.com',  '1',  '21',  'testusr',  'testpwd',  'public_html/',  '1'
);

INSERT INTO  `godeploy`.`servers` (
`id` ,
`name` ,
`hostname` ,
`connection_types_id` ,
`port` ,
`username` ,
`password` ,
`remote_path` ,
`projects_id`
)
VALUES (
2 ,  'Live server',  'www.somesite.com',  '1',  '21',  'testusr',  'testpwd',  'public_html/',  '1'
);