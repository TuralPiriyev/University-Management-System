<div class="messages-container">
    <h1 class="messages-header">Mesajlar</h1>

    <?php
    // Nümunə mesajlar - verilənlər bazasından gələcək
    $messages = [
        [
            'id' => 1,
            'sender' => 'Universitet Rektorluğu',
            'subject' => 'Yeni tədris ili haqqında məlumat',
            'preview' => 'Hörmətli tələbələr! 2025-2026 tədris ili 15 sentyabr tarixində başlayacaq. Dərslər saatı və cədvəl haqqında ətraflı məlumat tezliklə açıqlanacaq...',
            'date' => '02 Noyabr 2025, 14:30',
            'unread' => true
        ],
        [
            'id' => 2,
            'sender' => 'Tələbə İşləri Şöbəsi',
            'subject' => 'Təqaüd ödənişləri',
            'preview' => 'Bu ay üçün təqaüd ödənişləri 10 noyabr tarixində bank hesablarınıza köçürüləcək. Əlavə suallarınız üçün bizimlə əlaqə saxlaya bilərsiniz.',
            'date' => '01 Noyabr 2025, 10:15',
            'unread' => true
        ],
        [
            'id' => 3,
            'sender' => 'Kitabxana İdarəsi',
            'subject' => 'Kitabların qaytarılması',
            'preview' => 'Kitabxanadan götürdüyünüz kitabların qaytarılma müddəti yaxınlaşır. Zəhmət olmasa kitabları vaxtında qaytarmağınızı xahiş edirik.',
            'date' => '30 Oktyabr 2025, 16:45',
            'unread' => false
        ],
        [
            'id' => 4,
            'sender' => 'Təhsil Hissəsi',
            'subject' => 'İmtahan cədvəli',
            'preview' => 'Yay semestri üçün imtahan cədvəli hazırlanıb. Cədvəl ilə universitetin rəsmi saytında tanış ola bilərsiniz.',
            'date' => '28 Oktyabr 2025, 09:20',
            'unread' => false
        ]
    ];

    if (empty($messages)): ?>
        <div class="empty-messages">
            <div class="empty-messages-icon">📭</div>
            <div class="empty-messages-text">Mesaj qutusu boşdur</div>
            <div class="empty-messages-subtext">Hələ ki, heç bir mesaj yoxdur</div>
        </div>
    <?php else: ?>
        <div class="messages-list">
            <?php foreach ($messages as $message): ?>
                <div class="message-card <?php echo $message['unread'] ? 'unread' : ''; ?>">
                    <div class="message-header">
                        <div class="message-sender">
                            <div class="sender-icon">🏛️</div>
                            <div class="sender-info">
                                <div class="sender-name"><?php echo $message['sender']; ?></div>
                                <div class="message-date"><?php echo $message['date']; ?></div>
                            </div>
                        </div>
                        <?php if ($message['unread']): ?>
                            <div class="message-badge">Yeni</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="message-subject"><?php echo $message['subject']; ?></div>
                    <div class="message-preview"><?php echo $message['preview']; ?></div>
                    
                    <div class="message-footer">
                        <div class="message-action">
                            📖 Oxu
                        </div>
                        <div class="message-action">
                            🗑️ Sil
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>