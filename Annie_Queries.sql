/*ALTER TABLE member
ALTER COLUMN passwd 
TYPE VARCHAR(50);*/
SELECT *
FROM member;

UPDATE member
SET pw_salt = 't2nboiqu52'
WHERE memberno = 1;

