<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum CompanyMainDocuments: string
{
    use EnumParser;

    case tax_id_number_files = 'tax_id_number_files'; // TIN
    case charter_files = 'charter_files'; // Nizamnamə
    case extract_files = 'extract_files'; // Çıxarış
    case director_id_card_files = 'director_id_card_files'; // Direktorun şəxsiyyət vəsiqəsi
    case creators_files = 'creators_files'; // Yaradıcıların şəxsiyyət vəsiqələri
    case fixed_asset_files = 'fixed_asset_files'; // Şirkətin əsas vasaitləri
    case founding_decision_files = 'founding_decision_files'; // Təsisçi qərarı faylları
}
