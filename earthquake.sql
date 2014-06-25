SELECT ASTEXT( A.SHAPE ) , location, state
FROM  `earth_quakes` A,  `state_borders` B
WHERE CONTAINS( B.SHAPE, A.SHAPE ) 
AND state =  'Texas'
