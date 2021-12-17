docker exec LamodaPuzzle_php composer install
docker exec LamodaPuzzle_php symfony console d:d:c --if-not-exists
docker exec LamodaPuzzle_php symfony console d:m:m -n
docker exec LamodaPuzzle_php symfony console d:f:l -n