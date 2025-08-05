-- Fix car image paths to match actual SVG files
-- Run this after importing the main database setup

-- Update car image paths to match existing SVG files
UPDATE cars SET image = 'images/cars/honda-civic-2019.svg' WHERE id = 1; -- 2018 Honda Civic LX
UPDATE cars SET image = 'images/cars/toyota-corolla-2018.svg' WHERE id = 2; -- 2019 Toyota Corolla LE
UPDATE cars SET image = 'images/cars/ford-escape-2018.svg' WHERE id = 3; -- 2017 Ford Escape SE
UPDATE cars SET image = 'images/cars/nissan-altima-2020.svg' WHERE id = 4; -- 2020 Nissan Sentra SV
UPDATE cars SET image = 'images/cars/chevrolet-malibu-2017.svg' WHERE id = 5; -- 2016 Chevrolet Malibu LT
UPDATE cars SET image = 'images/cars/hyundai-tucson-2018.svg' WHERE id = 6; -- 2019 Hyundai Elantra SEL
UPDATE cars SET image = 'images/cars/kia-sorento-2019.svg' WHERE id = 7; -- 2018 Kia Forte LX
UPDATE cars SET image = 'images/cars/mazda-cx5-2020.svg' WHERE id = 8; -- 2020 Mazda3 Preferred
UPDATE cars SET image = 'images/cars/subaru-outback-2018.svg' WHERE id = 9; -- 2017 Subaru Impreza Premium
UPDATE cars SET image = 'images/cars/volkswagen-jetta-2019.svg' WHERE id = 10; -- 2019 Volkswagen Jetta S
UPDATE cars SET image = 'images/cars/toyota-rav4-2020.svg' WHERE id = 11; -- 2018 Toyota Camry LE
UPDATE cars SET image = 'images/cars/honda-accord-2019.svg' WHERE id = 12; -- 2020 Honda Accord LX
UPDATE cars SET image = 'images/cars/ford-f150-2017.svg' WHERE id = 13; -- 2017 Ford Focus SE
UPDATE cars SET image = 'images/cars/chevrolet-silverado-2019.svg' WHERE id = 14; -- 2019 Chevrolet Cruze LT
UPDATE cars SET image = 'images/cars/nissan-altima-2020.svg' WHERE id = 15; -- 2018 Nissan Altima 2.5 S
UPDATE cars SET image = 'images/cars/kia-sorento-2019.svg' WHERE id = 16; -- 2020 Kia Rio LX
UPDATE cars SET image = 'images/cars/hyundai-tucson-2018.svg' WHERE id = 17; -- 2017 Hyundai Sonata SE
UPDATE cars SET image = 'images/cars/mazda-cx5-2020.svg' WHERE id = 18; -- 2019 Mazda CX-5 Sport
UPDATE cars SET image = 'images/cars/subaru-outback-2018.svg' WHERE id = 19; -- 2018 Subaru Outback 2.5i
UPDATE cars SET image = 'images/cars/volkswagen-jetta-2019.svg' WHERE id = 20; -- 2020 Volkswagen Passat 2.0T

-- Additional cars if they exist
UPDATE cars SET image = 'images/cars/tesla-model3-2020.svg' WHERE id = 21;
UPDATE cars SET image = 'images/cars/bmw-x3-2019.svg' WHERE id = 22;
UPDATE cars SET image = 'images/cars/audi-a4-2019.svg' WHERE id = 23;
UPDATE cars SET image = 'images/cars/mercedes-c300-2017.svg' WHERE id = 24;
UPDATE cars SET image = 'images/cars/lexus-rx350-2017.svg' WHERE id = 25;
UPDATE cars SET image = 'images/cars/acura-tlx-2018.svg' WHERE id = 26;
UPDATE cars SET image = 'images/cars/jeep-wrangler-2018.svg' WHERE id = 27;
UPDATE cars SET image = 'images/cars/dodge-charger-2018.svg' WHERE id = 28;