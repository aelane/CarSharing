
//Gets User Details
   CREATE OR REPLACE FUNCTION carsharing.getUserDetails(nn VARCHAR(10)) RETURNS SETOF refcursor AS $$
    DECLARE
      ref1 refcursor;           
      ref2 refcursor;
      ref3 refcursor;
      ref4 refcursor;                             
    BEGIN
      OPEN ref1 FOR SELECT givenname || ' ' || familyname FROM member WHERE nickname = nn;
      RETURN NEXT ref1;                                                                           
 
      OPEN ref2 FOR SELECT homepod FROM member WHERE nickname = nn;
      RETURN NEXT ref2;   

      OPEN ref3 FOR SELECT address FROM member WHERE nickname = nn;
      RETURN NEXT ref3;   

      OPEN ref4 FOR SELECT COUNT(*) FROM member JOIN booking ON memberno = madeby WHERE nickname = nn;
      RETURN NEXT ref4;                                                                  
    END;
    $$ LANGUAGE plpgsql;