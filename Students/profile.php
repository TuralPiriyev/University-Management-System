<?php
// Tələbə məlumatları (Məlumat bazasından gələcək)
$student = [
    'ad' => 'Ayşə',
    'soyad' => 'Məmmədova',
    'ata_adi' => 'Elxan',
    'fakulte' => 'İnformasiya Texnologiyaları',
    'ixtisas' => 'Kompüter Mühəndisliyi',
    'giris_ili' => '2023',
    'tedris_dili' => 'Azərbaycan',
    'tehsil_forması' => 'Pullu',
    'email' => 'ayshe.memmedova@student.edu.az',
    'telefon' => '+994 50 123 45 67',
    'dogum_tarixi' => '15.03.2005',
    'dogum_yeri' => 'Bakı',
    'unvan' => 'Bakı şəhəri, Nəsimi rayonu',
    'profil_sekli' => 'assets/images/default-avatar.png' // Şəkil yolu
];

$family = [
    'ata_adi' => 'Elxan Məmmədov',
    'ata_telefon' => '+994 50 234 56 78',
    'ana_adi' => 'Günel Məmmədova',
    'ana_telefon' => '+994 55 345 67 89',
    'qohum_adi' => 'Nigar Məmmədova',
    'qohum_elaqe' => 'Bacı',
    'qohum_telefon' => '+994 70 456 78 90'
];
?>

<div class="profile-container">
    <div class="profile-header">
        <h2><i class="fas fa-user-circle"></i> Profil Məlumatları</h2>
    </div>

    <div class="profile-content">
        <!-- Şəxsi Məlumatlar -->
        <div class="profile-section">
            <div class="section-header">
                <i class="fas fa-id-card"></i>
                <h3>Şəxsi Məlumatlar</h3>
            </div>
            
            <div class="profile-photo">
                <img src="<?php echo $student['profil_sekli']; ?>" alt="Profil şəkli">
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Ad:</span>
                    <span class="value"><?php echo $student['ad']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Soyad:</span>
                    <span class="value"><?php echo $student['soyad']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Ata adı:</span>
                    <span class="value"><?php echo $student['ata_adi']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Doğum tarixi:</span>
                    <span class="value"><?php echo $student['dogum_tarixi']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Doğum yeri:</span>
                    <span class="value"><?php echo $student['dogum_yeri']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Ünvan:</span>
                    <span class="value"><?php echo $student['unvan']; ?></span>
                </div>
            </div>
        </div>

        <!-- Təhsil Məlumatları -->
        <div class="profile-section">
            <div class="section-header">
                <i class="fas fa-graduation-cap"></i>
                <h3>Təhsil Məlumatları</h3>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Fakultə:</span>
                    <span class="value"><?php echo $student['fakulte']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">İxtisas:</span>
                    <span class="value"><?php echo $student['ixtisas']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Giriş ili:</span>
                    <span class="value"><?php echo $student['giris_ili']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Təhsil forması:</span>
                    <span class="value badge <?php echo $student['tehsil_forması'] == 'Pullu' ? 'badge-warning' : 'badge-success'; ?>">
                        <?php echo $student['tehsil_forması']; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="label">Tədris dili:</span>
                    <span class="value"><?php echo $student['tedris_dili']; ?></span>
                </div>
            </div>
        </div>

        <!-- Əlaqə Məlumatları -->
        <div class="profile-section">
            <div class="section-header">
                <i class="fas fa-envelope"></i>
                <h3>Əlaqə Məlumatları</h3>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo $student['email']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Telefon:</span>
                    <span class="value"><?php echo $student['telefon']; ?></span>
                </div>
            </div>
        </div>

        <!-- Ailə Məlumatları -->
        <div class="profile-section">
            <div class="section-header">
                <i class="fas fa-users"></i>
                <h3>Ailə Məlumatları</h3>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Ata:</span>
                    <span class="value"><?php echo $family['ata_adi']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Atanın telefonu:</span>
                    <span class="value"><?php echo $family['ata_telefon']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Ana:</span>
                    <span class="value"><?php echo $family['ana_adi']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Ananın telefonu:</span>
                    <span class="value"><?php echo $family['ana_telefon']; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Qohum (Təcili hallar üçün):</span>
                    <span class="value"><?php echo $family['qohum_adi']; ?> (<?php echo $family['qohum_elaqe']; ?>)</span>
                </div>
                <div class="info-item">
                    <span class="label">Qohum telefon:</span>
                    <span class="value"><?php echo $family['qohum_telefon']; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>