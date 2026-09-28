<?php

$file = 'resources/views/frontend/tours/checkout.blade.php';
$content = file_get_contents($file);

$startMarker = '// --- AUTO ROOM SUGGESTION LOGIC ---';
$endMarker = 'function applyRoomAllocation(strategy) {';

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker, $startPos);

if ($startPos !== false && $endPos !== false) {
    $newLogic = <<<'EOF'
// --- AUTO ROOM SUGGESTION LOGIC ---
    function generateRoomAllocation(strategy) {
        let A = parseInt("{{ $adults }}") || 0;
        let C = parseInt("{{ $children }}") || 0;
        
        let singleRoomsCount = 1;
        let extraBedsCount = 0;
        
        let selectedAcc = document.querySelector('.accommodation-radio:checked');
        if (selectedAcc) {
            let baseCap = parseInt(selectedAcc.dataset.baseCapacity) || 2;
            let maxCap = parseInt(selectedAcc.dataset.maxCapacity) || 3;
            
            // Số phòng cơ bản dựa trên người lớn
            singleRoomsCount = Math.ceil(A / baseCap);
            
            // Đảm bảo đủ sức chứa tối đa cho cả trẻ em
            if (singleRoomsCount * maxCap < A + C) {
                singleRoomsCount = Math.ceil((A + C) / maxCap);
            }
            
            // Tính số giường phụ cần thiết (số người vượt quá base_capacity của các phòng)
            let overflow = (A + C) - (singleRoomsCount * baseCap);
            if (overflow > 0) {
                extraBedsCount = Math.min(overflow, singleRoomsCount * (maxCap - baseCap));
            }
        }
        
        // Update the visual UI inputs
        const srInput = document.getElementById('single_rooms_count');
        const ebInput = document.getElementById('extra_beds_count');
        if (srInput) srInput.value = singleRoomsCount;
        if (ebInput) ebInput.value = extraBedsCount;
        
        return {
            singleRoomsCount: singleRoomsCount,
            extraBedsCount: extraBedsCount,
            rooms: []
        };
    }

    function applyRoomAllocation(strategy) {
EOF;

    $content = substr_replace($content, $newLogic, $startPos, $endPos - $startPos + strlen($endMarker));
    file_put_contents($file, $content);
    echo "Replaced successfully\n";
} else {
    echo "Markers not found\n";
}
