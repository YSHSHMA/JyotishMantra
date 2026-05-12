<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditorium Admin - Row & Column Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: linear-gradient(135deg, #1a2a6c, #0d47a1);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h1 i {
            font-size: 1.8rem;
        }

        .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .admin-panel {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .control-panel {
            flex: 1;
            min-width: 300px;
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .visualization {
            flex: 2;
            min-width: 500px;
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
        }

        .section-title {
            font-size: 1.4rem;
            color: #1a2a6c;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eaeaea;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #0d47a1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        input,
        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border 0.3s;
        }

        input:focus,
        select:focus {
            border-color: #1a2a6c;
            outline: none;
            box-shadow: 0 0 0 2px rgba(26, 42, 108, 0.1);
        }

        .btn {
            background-color: #1a2a6c;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn:hover {
            background-color: #0d1a4d;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-full {
            width: 100%;
            margin-top: 10px;
        }

        .stage {
            background: linear-gradient(to bottom, #8B4513, #A0522D);
            height: 80px;
            margin: 0 auto 30px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            width: 80%;
        }

        .stage:after {
            content: "";
            position: absolute;
            top: -10px;
            left: 10%;
            width: 80%;
            height: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 50%;
        }

        .seating-area {
            flex-direction: column;
            align-items: center;
            gap: 15px;
            flex-grow: 1;
            overflow: auto;
            padding: 10px;
            /* display: flex; */
        }

        #seatingArea {
            width: 300px;
            height: 500px;
        }

        .row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .row-label {
            width: 40px;
            text-align: center;
            font-weight: bold;
            color: #555;
            background-color: #f8f9fa;
            padding: 8px 5px;
            border-radius: 4px;
        }

        .seat {
            width: 35px;
            height: 35px;
            background-color: #e9ecef;
            border-radius: 5px 5px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            color: #495057;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .seat:hover {
            transform: scale(1.1);
            background-color: #dee2e6;
        }

        .seat.selected {
            background-color: #4CAF50;
            color: white;
        }

        .seat.vip {
            background-color: #FFD700;
            color: #333;
        }

        .seat.accessible {
            background-color: #2196F3;
            color: white;
        }

        .seat.blocked {
            background-color: #6c757d;
            color: white;
            cursor: not-allowed;
        }

        .aisle {
            width: 50px;
        }

        .legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 3px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .row-controls {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .row-controls input {
            flex: 1;
        }

        .row-list {
            margin-top: 20px;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #eaeaea;
            border-radius: 6px;
            padding: 10px;
        }

        .row-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .row-item:last-child {
            border-bottom: none;
        }

        .row-item-info {
            display: flex;
            flex-direction: column;
        }

        .row-item-name {
            font-weight: 600;
        }

        .row-item-details {
            font-size: 0.85rem;
            color: #666;
        }

        .row-item-actions {
            display: flex;
            gap: 5px;
        }

        .status-message {
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
            text-align: center;
            font-weight: 500;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .admin-panel {
                flex-direction: column;
            }

            .control-panel,
            .visualization {
                min-width: 100%;
            }
        }

        .d-flex {
            display: flex;
            justify-content: space-around;
        }

        div {
            display: grid;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <?php 
        // Get all venue data
        $allVenues = json_decode($getData['all_venue_data'] ?? "[]", true);
        
        // Get existing auditorium layouts (array of all venue layouts)
        $allAuditoriumLayouts = json_decode($getData['layout_auditorium'] ?? "[]", true);
        
        // Initialize current venue layout data
        $currentVenueLayout = [];
        $selectedVenueId = null;
        
        // Check if we have a selected venue from the data
        if (isset($getData['id']) && $allVenues && is_array($allAuditoriumLayouts)) {
            // Get the current venue ID from the event data
            // Adjust this based on your actual data structure
            $selectedVenueId = $getData['venue_id'] ?? null;
            
            // Find the layout for the current venue
            if ($selectedVenueId) {
                foreach ($allAuditoriumLayouts as $layout) {
                    if (isset($layout['venue_id']) && $layout['venue_id'] == $selectedVenueId) {
                        $currentVenueLayout = $layout;
                        break;
                    }
                }
            }
            
            // If no layout found, use the first one or empty
            if (empty($currentVenueLayout) && !empty($allAuditoriumLayouts) && isset($allAuditoriumLayouts[0])) {
                $currentVenueLayout = $allAuditoriumLayouts[0];
                $selectedVenueId = $currentVenueLayout['venue_id'] ?? null;
            }
        }
        ?>
        <header>
            <div class="d-flex">
                <div>
                    <h1><i class="fas fa-user-cog"></i> Auditorium Admin Panel</h1>
                    <p class="subtitle">Manage Rows, Columns, and Seating Layout</p>
                </div>
                <div>
                    <span>
                        <a class="btn float-end" href="{{ route('event-vendor.event-management.event-list') }}">Event List</a>
                </div>
                </span>
            </div>
        </header>

        <div class="admin-panel">
            <div class="control-panel">
                <h2 class="section-title"><i class="fas fa-sliders-h"></i> Layout Configuration</h2>
                <div class="form-group">
                    <label for="">Select Venue</label>
                    <select id="venue_ids">
                        <option value="">Select Venue</option>
                        @if($allVenues && is_array($allVenues))
                            @foreach($allVenues as $venue)
                                @php
                                    $layoutAuditorium = '{}';
                                    // Find existing layout for this venue
                                    if (is_array($allAuditoriumLayouts)) {
                                        foreach ($allAuditoriumLayouts as $layout) {
                                            if (isset($layout['venue_id']) && $layout['venue_id'] == $venue['id']) {
                                                $layoutAuditorium = htmlspecialchars(json_encode($layout));
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <option value="{{ $venue['id'] }}" 
                                        data-layout_auditorium="{{ $layoutAuditorium }}"
                                        {{ ($selectedVenueId == $venue['id']) ? 'selected' : '' }}>
                                    {{ ($venue['en_event_venue_full_address'] ?? $venue['en_event_venue'] ?? 'Venue ' . $venue['id']) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label>Staging Types</label>
                    <select id="stage_type">
                        <option value="">Select Stage Type</option>
                        <option value="1" {{ (($currentVenueLayout['stage_type'] ?? '') == '1') ? 'selected' : '' }}>Proscenium/Stage Front</option>
                        <option value="2" {{ (($currentVenueLayout['stage_type'] ?? '') == '2') ? 'selected' : '' }}>Round/Runaround Stage</option>
                        <option value="3" {{ (($currentVenueLayout['stage_type'] ?? '') == '3') ? 'selected' : '' }}>Thrust Stage</option>
                        <option value="4" {{ (($currentVenueLayout['stage_type'] ?? '') == '4') ? 'selected' : '' }}>Arena/In-the-Round</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="totalRows"><i class="fas fa-bars"></i> Total Rows</label>
                    <input type="number" id="totalRows" min="1" max="50" value="{{ $currentVenueLayout['total_rows'] ?? '10' }}">
                </div>

                <div class="form-group">
                    <label for="seatsPerRow"><i class="fas fa-chair"></i> Seats Per Row</label>
                    <input type="number" id="seatsPerRow" min="1" max="30" value="{{ $currentVenueLayout['seats_per_row'] ?? '10' }}">
                </div>

                <div class="form-group">
                    <label for="aislePositions"><i class="fas fa-walking"></i> Aisle Positions (comma separated seat numbers)</label>
                    <input type="text" id="aislePositions" placeholder="e.g., 4,8" value="{{ isset($currentVenueLayout['aisle_positions']) && is_array($currentVenueLayout['aisle_positions']) ? implode(',', $currentVenueLayout['aisle_positions']) : '4,8' }}">
                </div>

                <div class="form-group">
                    <label for="rowStart"><i class="fas fa-font"></i> Row Naming Start</label>
                    <select id="rowStart">
                        <option value="A" {{ (($currentVenueLayout['row_start'] ?? '') == 'A') ? 'selected' : '' }}>A, B, C, ...</option>
                        <option value="A1" {{ (($currentVenueLayout['row_start'] ?? '') == 'A1') ? 'selected' : '' }}>A1, A2, A3, ...</option>
                        <option value="1" {{ (($currentVenueLayout['row_start'] ?? '') == '1') ? 'selected' : '' }}>1, 2, 3, ...</option>
                    </select>
                </div>

                <button class="btn btn-full" id="generateLayout">
                    <i class="fas fa-sync-alt"></i> Generate Layout
                </button>

                <h2 class="section-title" style="margin-top: 30px;"><i class="fas fa-edit"></i> Row Management</h2>

                <div class="form-group">
                    <label for="rowName">Row Name / Number</label>
                    <input type="text" id="rowName" placeholder="Enter row identifier">
                </div>

                <div class="form-group">
                    <label for="rowType">Row Type</label>
                    <select id="rowType">
                        <option value="standard">Standard</option>
                        <option value="vip">VIP</option>
                        <option value="accessible">Accessible</option>
                    </select>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-success" id="addRow">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                    <button class="btn btn-secondary" id="updateRow">
                        <i class="fas fa-edit"></i> Update Row
                    </button>
                </div>

                <div class="row-list" id="rowList">

                </div>

                <div id="statusMessage" class="status-message" style="display: none;"></div>
            </div>

            <div class="visualization">
                <h2 class="section-title"><i class="fas fa-eye"></i> Layout Preview</h2>

                <div class="stage">
                    STAGE
                </div>
                <div class="seating-area">
                    <div id="seatingArea">
                    </div>
                </div>

                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #e9ecef;"></div>
                        <span>Standard Seat</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #FFD700;"></div>
                        <span>VIP Seat</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #2196F3;"></div>
                        <span>Accessible Seat</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #4CAF50;"></div>
                        <span>Selected Row</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #6c757d;"></div>
                        <span>Blocked Seat</span>
                    </div>
                </div>

                <div class="action-buttons" style="justify-content: center; margin-top: 20px;">
                    <button class="btn" id="saveLayout">
                        <i class="fas fa-save"></i> Save Layout
                    </button>
                    <button class="btn btn-secondary" id="resetLayout">
                        <i class="fas fa-undo"></i> Reset Layout
                    </button>
                    <button class="btn btn-danger" id="clearLayout">
                        <i class="fas fa-trash"></i> Clear All
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const venueSelect = document.getElementById('venue_ids');
            const seatingArea = document.getElementById('seatingArea');
            const totalRowsInput = document.getElementById('totalRows');
            const seatsPerRowInput = document.getElementById('seatsPerRow');
            const aislePositionsInput = document.getElementById('aislePositions');
            const rowStartInput = document.getElementById('rowStart');
            const generateLayoutBtn = document.getElementById('generateLayout');
            const rowNameInput = document.getElementById('rowName');
            const rowTypeInput = document.getElementById('rowType');
            const addRowBtn = document.getElementById('addRow');
            const updateRowBtn = document.getElementById('updateRow');
            const rowList = document.getElementById('rowList');
            const saveLayoutBtn = document.getElementById('saveLayout');
            const resetLayoutBtn = document.getElementById('resetLayout');
            const clearLayoutBtn = document.getElementById('clearLayout');
            const statusMessage = document.getElementById('statusMessage');
            
            // Data
            let rowsData = [];
            let selectedRowId = null;
            let selectedVenueId = null;
            let blockedSeats = [];
            let allVenueLayouts = <?php echo json_encode($allAuditoriumLayouts ?: []); ?>;

            // Initialize
            initializeFromCurrentVenue();
            generateLayout();
            updateRowList();

            // Event Listeners
            venueSelect.addEventListener('change', handleVenueChange);
            generateLayoutBtn.addEventListener('click', generateLayout);
            addRowBtn.addEventListener('click', addRow);
            updateRowBtn.addEventListener('click', updateRow);
            saveLayoutBtn.addEventListener('click', saveLayout);
            resetLayoutBtn.addEventListener('click', resetLayout);
            clearLayoutBtn.addEventListener('click', clearLayout);

            // Functions
            function initializeFromCurrentVenue() {
                const currentVenueId = venueSelect.value;
                if (currentVenueId) {
                    selectedVenueId = currentVenueId;
                    loadVenueLayout(currentVenueId);
                }
            }

            function handleVenueChange() {
                selectedVenueId = venueSelect.value;
                if (selectedVenueId) {
                    loadVenueLayout(selectedVenueId);
                } else {
                    resetToDefaults();
                    showStatus('Please select a venue', 'info');
                }
            }

            function loadVenueLayout(venueId) {
                // Find existing layout for this venue
                const existingLayout = allVenueLayouts.find(layout => layout.venue_id == venueId);
                
                if (existingLayout) {
                    // Load existing layout data
                    if (existingLayout.total_rows) totalRowsInput.value = existingLayout.total_rows;
                    if (existingLayout.stage_type){
                        $('#stage_type').val(existingLayout.stage_type);
                    } 
                    if (existingLayout.seats_per_row) seatsPerRowInput.value = existingLayout.seats_per_row;
                    if (existingLayout.aisle_positions) {
                        if (Array.isArray(existingLayout.aisle_positions)) {
                            aislePositionsInput.value = existingLayout.aisle_positions.join(',');
                        } else {
                            aislePositionsInput.value = existingLayout.aisle_positions;
                        }
                    }
                    if (existingLayout.row_start) rowStartInput.value = existingLayout.row_start;
                    if (existingLayout.rows) rowsData = existingLayout.rows;
                    if (existingLayout.blocked_seats) blockedSeats = existingLayout.blocked_seats;
                    
                    showStatus('Loaded existing layout for selected venue', 'success');
                } else {
                    // Reset to defaults for new venue
                    resetToDefaults();
                    showStatus('No existing layout found. Creating new layout.', 'info');
                }
                
                updateRowList();
                generateLayout(blockedSeats);
            }

            function resetToDefaults() {
                totalRowsInput.value = '10';
                seatsPerRowInput.value = '12';
                aislePositionsInput.value = '4,8';
                rowStartInput.value = 'A';
                rowsData = [];
                blockedSeats = [];
                selectedRowId = null;
                rowNameInput.value = '';
                rowTypeInput.value = 'standard';
            }

            function generateLayout(blockedSeatsData = []) {
                const totalRows = parseInt(totalRowsInput.value) || 10;
                const seatsPerRow = parseInt(seatsPerRowInput.value) || 12;
                const aislePositions = aislePositionsInput.value.split(',')
                    .map(pos => parseInt(pos.trim()))
                    .filter(pos => !isNaN(pos));
                const rowStart = rowStartInput.value;

                seatingArea.innerHTML = '';

                for (let row = 1; row <= totalRows; row++) {
                    const rowElement = document.createElement('div');
                    rowElement.className = 'row';
                    rowElement.dataset.rowId = row;

                    const rowLabel = document.createElement('div');
                    rowLabel.className = 'row-label';
                    if (rowStart === 'A') {
                        rowLabel.textContent = getRowLabel(row);
                    } else if (rowStart === 'A1') {
                        rowLabel.textContent = `A${row}`;
                    } else {
                        rowLabel.textContent = row.toString();
                    }
                    rowElement.dataset.rowRowsname = rowLabel.textContent;
                    rowElement.appendChild(rowLabel);

                    for (let seat = 1; seat <= seatsPerRow; seat++) {
                        // Add aisle if needed
                        if (aislePositions.includes(seat)) {
                            const aisle = document.createElement('div');
                            aisle.className = 'aisle';
                            rowElement.appendChild(aisle);
                        }

                        const seatElement = document.createElement('div');
                        seatElement.className = 'seat';
                        seatElement.dataset.row = row;
                        seatElement.dataset.seat = seat;
                        seatElement.dataset.id = `${row}-${seat}`;
                        
                        const rowData = rowsData.find(r => r.id === row);
                        if (rowData) {
                            if (rowData.type === 'vip') {
                                seatElement.classList.add('vip');
                            } else if (rowData.type === 'accessible') {
                                seatElement.classList.add('accessible');
                            }
                        }
                        
                        // Check if seat is blocked
                        if (Array.isArray(blockedSeatsData) && blockedSeatsData.length > 0) {
                            const isBlocked = blockedSeatsData.some(blockedSeat =>
                                blockedSeat.id === `${row}-${seat}` ||
                                (parseInt(blockedSeat.row) === row && parseInt(blockedSeat.seat) === seat)
                            );
                            if (isBlocked) {
                                seatElement.classList.add('blocked');
                            }
                        }

                        seatElement.addEventListener('click', function() {
                            if (this.classList.contains('blocked')) {
                                this.classList.remove('blocked');
                                // Remove from blockedSeats array
                                blockedSeats = blockedSeats.filter(bs => 
                                    bs.id !== this.dataset.id && 
                                    !(parseInt(bs.row) === parseInt(this.dataset.row) && 
                                      parseInt(bs.seat) === parseInt(this.dataset.seat))
                                );
                            } else {
                                this.classList.add('blocked');
                                // Add to blockedSeats array
                                blockedSeats.push({
                                    id: this.dataset.id,
                                    row: parseInt(this.dataset.row),
                                    seat: parseInt(this.dataset.seat)
                                });
                            }
                        });

                        rowElement.appendChild(seatElement);
                    }

                    rowElement.addEventListener('click', function(e) {
                        if (!e.target.classList.contains('seat') && !e.target.classList.contains('aisle')) {
                            selectRow(row);
                        }
                    });

                    seatingArea.appendChild(rowElement);
                }

                showStatus('Layout generated successfully!', 'success');
            }

            function getRowLabel(rowNumber) {
                let result = '';
                let num = rowNumber;

                while (num > 0) {
                    num--;
                    result = String.fromCharCode(65 + (num % 26)) + result;
                    num = Math.floor(num / 26);
                }
                return result;
            }

            function selectRow(rowId) {
                document.querySelectorAll('.row').forEach(row => {
                    row.classList.remove('selected');
                });

                const rowElement = document.querySelector(`.row[data-row-id="${rowId}"]`);
                if (rowElement) {
                    rowElement.classList.add('selected');

                    const rowData = rowsData.find(r => r.id === rowId);
                    if (rowData) {
                        rowNameInput.value = rowData.name;
                        rowTypeInput.value = rowData.type;
                        selectedRowId = rowId;
                    } else {
                        rowNameInput.value = '';
                        rowTypeInput.value = 'standard';
                        selectedRowId = rowId;
                    }
                }
            }

            function addRow() {
                const rowName = rowNameInput.value.trim();
                const rowType = rowTypeInput.value;

                if (!rowName) {
                    showStatus('Please enter a row name!', 'error');
                    return;
                }

                if (!selectedRowId) {
                    showStatus('Please select a row first!', 'error');
                    return;
                }

                const existingIndex = rowsData.findIndex(r => r.id === selectedRowId);

                if (existingIndex !== -1) {
                    rowsData[existingIndex].name = rowName;
                    rowsData[existingIndex].type = rowType;
                    rowsData[existingIndex].rowname = $(`.row[data-row-id="${rowsData[existingIndex].id}"]`).data('row-rowsname');
                    showStatus(`Row ${rowName} updated successfully!`, 'success');
                } else {
                    rowsData.push({
                        id: selectedRowId,
                        name: rowName,
                        type: rowType,
                        rowname:$(`.row[data-row-id="${selectedRowId}"]`).data('row-rowsname'),
                    });
                    showStatus(`Row ${rowName} added successfully!`, 'success');
                }

                updateRowList();
                generateLayout(blockedSeats);
            }

            function updateRow() {
                if (!selectedRowId) {
                    showStatus('Please select a row first!', 'error');
                    return;
                }

                const rowData = rowsData.find(r => r.id === selectedRowId);
                if (rowData) {
                    rowNameInput.value = rowData.name;
                    rowTypeInput.value = rowData.type;
                } else {
                    showStatus('No data found for selected row!', 'error');
                }
            }

            function updateRowList() {
                rowList.innerHTML = '';

                if (rowsData.length === 0) {
                    rowList.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">No rows configured</div>';
                    return;
                }

                rowsData.sort((a, b) => a.id - b.id).forEach(row => {
                    const rowItem = document.createElement('div');
                    rowItem.className = 'row-item';

                    const rowInfo = document.createElement('div');
                    rowInfo.className = 'row-item-info';

                    const rowName = document.createElement('div');
                    rowName.className = 'row-item-name';
                    rowName.textContent = row.name;

                    const rowDetails = document.createElement('div');
                    rowDetails.className = 'row-item-details';
                    rowDetails.textContent = `ID: ${row.id} | Type: ${row.type.toUpperCase()}`;

                    rowInfo.appendChild(rowName);
                    rowInfo.appendChild(rowDetails);

                    const rowActions = document.createElement('div');
                    rowActions.className = 'row-item-actions';

                    const editBtn = document.createElement('button');
                    editBtn.className = 'btn btn-secondary';
                    editBtn.innerHTML = '<i class="fas fa-edit"></i>';
                    editBtn.title = 'Edit Row';
                    editBtn.addEventListener('click', function() {
                        selectRow(row.id);
                    });

                    const deleteBtn = document.createElement('button');
                    deleteBtn.className = 'btn btn-danger';
                    deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                    deleteBtn.title = 'Delete Row';
                    deleteBtn.addEventListener('click', function() {
                        rowsData = rowsData.filter(r => r.id !== row.id);
                        updateRowList();
                        generateLayout(blockedSeats);
                        showStatus(`Row ${row.name} deleted successfully!`, 'success');
                    });

                    rowActions.appendChild(editBtn);
                    rowActions.appendChild(deleteBtn);

                    rowItem.appendChild(rowInfo);
                    rowItem.appendChild(rowActions);

                    rowList.appendChild(rowItem);
                });
            }

            function saveLayout() {
                // Validate venue selection
                if (!selectedVenueId) {
                    showStatus('Please select a venue first!', 'error');
                    return;
                }

                // Get current blocked seats from UI
                const currentBlockedSeats = [];
                document.querySelectorAll('.seat.blocked').forEach(seat => {
                    currentBlockedSeats.push({
                        id: seat.dataset.id,
                        row: parseInt(seat.dataset.row),
                        seat: parseInt(seat.dataset.seat)
                    });
                });

                const stageTypeSelect = document.getElementById('stage_type');
                const currentLayout = {
                    venue_id: selectedVenueId,
                    stage_type: stageTypeSelect.value,
                    total_rows: parseInt(totalRowsInput.value) || 10,
                    seats_per_row: parseInt(seatsPerRowInput.value) || 12,
                    aisle_positions: aislePositionsInput.value.split(',').map(pos => parseInt(pos.trim())).filter(pos => !isNaN(pos)),
                    row_start: rowStartInput.value,
                    rows: rowsData,
                    timestamp: new Date().toISOString(),
                    blocked_seats: currentBlockedSeats,
                    total_seats: (parseInt(totalRowsInput.value) || 10) * (parseInt(seatsPerRowInput.value) || 12),
                    available_seats: ((parseInt(totalRowsInput.value) || 10) * (parseInt(seatsPerRowInput.value) || 12)) - currentBlockedSeats.length,
                };
                
                // Update or add the layout to the array
                const existingIndex = allVenueLayouts.findIndex(layout => layout.venue_id == selectedVenueId);
                
                if (existingIndex !== -1) {
                    allVenueLayouts[existingIndex] = currentLayout;
                    showStatus(`Layout updated for selected venue`, 'success');
                } else {
                    allVenueLayouts.push(currentLayout);
                    showStatus(`New layout created for selected venue`, 'success');
                }
                
                $.ajax({
                    url: "{{ route('event-vendor.event-management.store-auditorium',['id'=>$getData['id']]) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data: JSON.stringify(allVenueLayouts), // Send the entire array
                    },
                    success: function(response) {
                        showStatus('All layouts saved successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = `{{ route('event-vendor.event-management.event-list') }}`;
                        }, 1500);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving layout:', error);
                        showStatus('Error saving layout: ' + (xhr.responseJSON?.message || error), 'error');
                    }
                });
            }

            function resetLayout() {
                if (!selectedVenueId) {
                    showStatus('Please select a venue first!', 'error');
                    return;
                }
                
                if (confirm('Reset layout for this venue to default values?')) {
                    resetToDefaults();
                    updateRowList();
                    generateLayout();
                    showStatus('Layout reset to default values!', 'success');
                }
            }

            function clearLayout() {
                if (!selectedVenueId) {
                    showStatus('Please select a venue first!', 'error');
                    return;
                }
                
                if (confirm('Are you sure you want to clear all rows and settings for this venue?')) {
                    rowsData = [];
                    blockedSeats = [];
                    selectedRowId = null;
                    rowNameInput.value = '';
                    rowTypeInput.value = 'standard';

                    updateRowList();
                    generateLayout();
                    showStatus('All layout data cleared for this venue!', 'success');
                }
            }

            function showStatus(message, type) {
                statusMessage.textContent = message;
                statusMessage.className = `status-message ${type}`;
                statusMessage.style.display = 'block';

                setTimeout(() => {
                    statusMessage.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</body>
</html>