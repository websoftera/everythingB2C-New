-- Correct the shared category label and URL spelling.
UPDATE categories
SET name = 'School Stationery', slug = 'school-stationery'
WHERE name = 'School Stationary' OR slug = 'school-stationary';
