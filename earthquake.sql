SELECT ASTEXT( A.SHAPE ) , location, state
FROM  `earth_quakes` A,  `state_borders` B
WHERE CONTAINS( A.SHAPE, B.SHAPE ) 
AND state =  'Texas'