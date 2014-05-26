/*ALTER TABLE member
ALTER COLUMN passwd 
TYPE VARCHAR(50);*/
SELECT *
FROM member;

UPDATE member
SET pw_salt = 't2nboiqu52'
WHERE memberno = 1;

/*Indices*/
/*For Login Page*/
CREATE INDEX loginIndex
ON member (nickname, password);

INSERT INTO review VALUES (1, 'AT61LA', CURRENT_DATE, 4, 'This is a great car');
INSERT INTO review VALUES (3, 'AT61LA', CURRENT_DATe, 1, 'I can not stand this car');
INSERT INTO review VALUES (12, 'AT61LA', CURRENT_DATE, 5, 'This is a wonderful car');
INSERT INTO review VALUES (7, 'AT61LA', CURRENT_DATE, 4, 'I love this car');
INSERT INTO review VALUES (29, 'AT61LA', CURRENT_DATE, 1, 'I will never drive this car again');


CREATE carsharing."getMemberFullName"("nN" character varying[])
  RETURNS character varying[] AS
$BODY$DECLARE
      name refcursor;                                                   
BEGIN
	OPEN name FOR SELECT givenname || ' ' || familyname FROM member WHERE nickname = nN;
	RETURN name;
END;$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION carsharing."getMemberFullName"(character varying[])
  OWNER TO alan3194;
/*carsharing."getMemberFullName"(character varying[]) IS 'First and last name of a member as a single string
Uses nickname to find member' */
