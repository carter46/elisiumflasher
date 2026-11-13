<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

/**
 * All 54 African countries for local transfers
 */
$africanCountries = [
    ['code' => 'DZ', 'name' => 'Algeria'],
    ['code' => 'AO', 'name' => 'Angola'],
    ['code' => 'BJ', 'name' => 'Benin'],
    ['code' => 'BW', 'name' => 'Botswana'],
    ['code' => 'BF', 'name' => 'Burkina Faso'],
    ['code' => 'BI', 'name' => 'Burundi'],
    ['code' => 'CV', 'name' => 'Cabo Verde'],
    ['code' => 'CM', 'name' => 'Cameroon'],
    ['code' => 'CF', 'name' => 'Central African Republic'],
    ['code' => 'TD', 'name' => 'Chad'],
    ['code' => 'KM', 'name' => 'Comoros'],
    ['code' => 'CG', 'name' => 'Republic of the Congo'],
    ['code' => 'CD', 'name' => 'DR Congo'],
    ['code' => 'DJ', 'name' => 'Djibouti'],
    ['code' => 'EG', 'name' => 'Egypt'],
    ['code' => 'GQ', 'name' => 'Equatorial Guinea'],
    ['code' => 'ER', 'name' => 'Eritrea'],
    ['code' => 'SZ', 'name' => 'Eswatini'],
    ['code' => 'ET', 'name' => 'Ethiopia'],
    ['code' => 'GA', 'name' => 'Gabon'],
    ['code' => 'GM', 'name' => 'Gambia'],
    ['code' => 'GH', 'name' => 'Ghana'],
    ['code' => 'GN', 'name' => 'Guinea'],
    ['code' => 'GW', 'name' => 'Guinea-Bissau'],
    ['code' => 'CI', 'name' => 'Ivory Coast'],
    ['code' => 'KE', 'name' => 'Kenya'],
    ['code' => 'LS', 'name' => 'Lesotho'],
    ['code' => 'LR', 'name' => 'Liberia'],
    ['code' => 'LY', 'name' => 'Libya'],
    ['code' => 'MG', 'name' => 'Madagascar'],
    ['code' => 'MW', 'name' => 'Malawi'],
    ['code' => 'ML', 'name' => 'Mali'],
    ['code' => 'MR', 'name' => 'Mauritania'],
    ['code' => 'MU', 'name' => 'Mauritius'],
    ['code' => 'MA', 'name' => 'Morocco'],
    ['code' => 'MZ', 'name' => 'Mozambique'],
    ['code' => 'NA', 'name' => 'Namibia'],
    ['code' => 'NE', 'name' => 'Niger'],
    ['code' => 'NG', 'name' => 'Nigeria'],
    ['code' => 'RW', 'name' => 'Rwanda'],
    ['code' => 'ST', 'name' => 'Sao Tome and Principe'],
    ['code' => 'SN', 'name' => 'Senegal'],
    ['code' => 'SC', 'name' => 'Seychelles'],
    ['code' => 'SL', 'name' => 'Sierra Leone'],
    ['code' => 'SO', 'name' => 'Somalia'],
    ['code' => 'ZA', 'name' => 'South Africa'],
    ['code' => 'SS', 'name' => 'South Sudan'],
    ['code' => 'SD', 'name' => 'Sudan'],
    ['code' => 'TZ', 'name' => 'Tanzania'],
    ['code' => 'TG', 'name' => 'Togo'],
    ['code' => 'TN', 'name' => 'Tunisia'],
    ['code' => 'UG', 'name' => 'Uganda'],
    ['code' => 'ZM', 'name' => 'Zambia'],
    ['code' => 'ZW', 'name' => 'Zimbabwe'],
];

/**
 * International countries: UK, US, CA + European countries (CH, DE, ES, IT, FR, DK, etc.)
 */
$internationalCountries = [
    ['code' => 'GB', 'name' => 'United Kingdom'],
    ['code' => 'US', 'name' => 'United States'],
    ['code' => 'CA', 'name' => 'Canada'],
    ['code' => 'CH', 'name' => 'Switzerland'],
    ['code' => 'DE', 'name' => 'Germany'],
    ['code' => 'ES', 'name' => 'Spain'],
    ['code' => 'IT', 'name' => 'Italy'],
    ['code' => 'FR', 'name' => 'France'],
    ['code' => 'DK', 'name' => 'Denmark'],
    ['code' => 'AT', 'name' => 'Austria'],
    ['code' => 'NL', 'name' => 'Netherlands'],
    ['code' => 'BE', 'name' => 'Belgium'],
    ['code' => 'SE', 'name' => 'Sweden'],
    ['code' => 'NO', 'name' => 'Norway'],
    ['code' => 'FI', 'name' => 'Finland'],
    ['code' => 'PT', 'name' => 'Portugal'],
    ['code' => 'IE', 'name' => 'Ireland'],
    ['code' => 'PL', 'name' => 'Poland'],
    ['code' => 'CZ', 'name' => 'Czech Republic'],
    ['code' => 'GR', 'name' => 'Greece'],
    ['code' => 'HU', 'name' => 'Hungary'],
    ['code' => 'RO', 'name' => 'Romania'],
    ['code' => 'AE', 'name' => 'United Arab Emirates'],
    ['code' => 'SA', 'name' => 'Saudi Arabia'],
    ['code' => 'QA', 'name' => 'Qatar'],
    ['code' => 'KW', 'name' => 'Kuwait'],
    ['code' => 'BH', 'name' => 'Bahrain'],
    ['code' => 'JP', 'name' => 'Japan'],
    ['code' => 'CN', 'name' => 'China'],
    ['code' => 'KR', 'name' => 'South Korea'],
    ['code' => 'SG', 'name' => 'Singapore'],
    ['code' => 'HK', 'name' => 'Hong Kong'],
    ['code' => 'IN', 'name' => 'India'],
    ['code' => 'TH', 'name' => 'Thailand'],
    ['code' => 'MY', 'name' => 'Malaysia'],
    ['code' => 'ID', 'name' => 'Indonesia'],
    ['code' => 'PH', 'name' => 'Philippines'],
    ['code' => 'PK', 'name' => 'Pakistan'],
    ['code' => 'VN', 'name' => 'Vietnam'],
    ['code' => 'TW', 'name' => 'Taiwan'],
    ['code' => 'IL', 'name' => 'Israel'],
    ['code' => 'TR', 'name' => 'Turkey'],
    ['code' => 'OM', 'name' => 'Oman'],
    ['code' => 'JO', 'name' => 'Jordan'],
    ['code' => 'LB', 'name' => 'Lebanon'],
    ['code' => 'IQ', 'name' => 'Iraq'],
    ['code' => 'MX', 'name' => 'Mexico'],
    ['code' => 'BR', 'name' => 'Brazil'],
    ['code' => 'AR', 'name' => 'Argentina'],
    ['code' => 'CO', 'name' => 'Colombia'],
    ['code' => 'LU', 'name' => 'Luxembourg'],
    ['code' => 'MT', 'name' => 'Malta'],
    ['code' => 'ZA', 'name' => 'South Africa'],
    ['code' => 'NG', 'name' => 'Nigeria'],
    ['code' => 'EG', 'name' => 'Egypt'],
    ['code' => 'TN', 'name' => 'Tunisia'],
    ['code' => 'KE', 'name' => 'Kenya'],
];

$scope = strtolower(trim((string) ($_GET['scope'] ?? 'local')));
if ($scope === 'international') {
    $countries = $internationalCountries;
} else {
    $countries = $africanCountries;
}

$q = mb_strtolower(trim((string) ($_GET['q'] ?? '')), 'UTF-8');
if ($q !== '') {
    $countries = array_values(array_filter(
        $countries,
        static function (array $row) use ($q): bool {
            $name = mb_strtolower($row['name'], 'UTF-8');
            $code = mb_strtolower($row['code'], 'UTF-8');
            return str_contains($name, $q) || str_contains($code, $q);
        }
    ));
}

json_response(['success' => true, 'countries' => $countries]);
