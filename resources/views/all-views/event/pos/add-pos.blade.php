@extends('layouts.back-end.app-event')
@section('title', translate('POS'))
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places">
    </script>
    <style>
        .remove-ticket {
            background: #ff4757;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            float: inline-end;
        }
    .cursor-not-allowed{
        cursor: no-drop;
    }
    </style>
@endpush
@section('content')
    @php
        use App\Utils\Helpers;
    @endphp
    <div class="content container-fluid">
        <div class="mb-3">
            <div class="row gy-2 align-items-center">
                <div class="col-sm">
                    <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/support-ticket.png') }}" alt="">
                        {{ translate('POS') }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="">Select Event Name</label>
                                <select name="event_id" id="event_ids" class="form-control" onchange="getEvents()">
                                    <option value="">Select Event</option>
                                    @if ($getEventData)
                                        @foreach ($getEventData as $va)
                                            <option value="{{ $va['id'] }}">{{ $va['event_name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="">Select Venue Name</label>
                                <select name="venue_id" id="venue_ids" class="form-control" onchange="getVenues()">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="">Select Date</label>
                                <select name="venue_date" id="venue_dates" class="form-control" onchange="getpackages()">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="">Select Package</label>
                                <select name="venue_package" id="venue_packages" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="">Select User Type</label>
                                <select name="type" id="types" class="form-control" onchange="getsponsors()">
                                    <option value="">Select Type</option>
                                    <option value="ticket">Ticket</option>
                                    <option value="sponsor">Sponsor</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group sponsor-data d-none">
                                <label for="">Select Sponsor Select</label>
                                <select name="sponsor_id[]" id="sponsor_ids" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="w-100">Number Of person</label>
                                <input type="number" id="ticketCount" min="1" max="10" value="1"
                                    class="form-control">
                            </div>
                        </div>
                        <!-- Add this to your HTML where you want the seating chart -->
                        <div class="row">
                            <div class="col-md-12">
                                <div id="seating-chart-container">
                                    <!-- Seats will be dynamically generated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Stage Display -->
                        <div id="stage-container" class="text-center my-4">
                            <div id="stage" class="stage"></div>
                            <p class="stage-label">STAGE</p>
                        </div>

                        <script>
                            // Function to extract and parse seating data from selected date
                            function getSeatingData() {
                                const dateSelect = document.getElementById('venue_dates');
                                const selectedOption = dateSelect.options[dateSelect.selectedIndex];
                                if (!selectedOption || !selectedOption.value) {
                                    console.log("No date selected");
                                    return null;
                                }

                                try {
                                    // Get the data-seats attribute (it's JSON string)
                                    const seatsData = selectedOption.getAttribute('data-seats');

                                    if (!seatsData) {
                                        console.log("No seating data available for this date");
                                        return null;
                                    }
                                    let allArray = JSON.parse(seatsData);
                                    let venue_id = $("#venue_dates").val();

                                    let venueData = allArray.find(item => item.venue_id === venue_id.toString());
                                    // Parse the JSON data
                                    return venueData;
                                } catch (error) {
                                    console.error("Error parsing seating data:", error);
                                    return null;
                                }
                            }

                            // Function to generate seating layout based on stage type
                            function generateSeatingLayout() {
                                const container = document.getElementById('seating-chart-container');
                                const stageContainer = document.getElementById('stage-container');

                                // Clear previous content
                                container.innerHTML = '';

                                // Get seating data
                                const seatingData = getSeatingData();

                                console.log(seatingData);
                                if (!seatingData) {
                                    container.innerHTML =
                                        '<div class="alert alert-info">Please select a date to view seating arrangement</div>';
                                    stageContainer.style.display = 'none';
                                    return;
                                }

                                // Show stage container
                                stageContainer.style.display = 'block';

                                // Generate stage based on stage_type
                                generateStage(seatingData.stage_type);

                                // Create main container for seats
                                const seatsWrapper = document.createElement('div');
                                seatsWrapper.className = 'seats-wrapper';
                                seatsWrapper.style.position = 'relative';
                                seatsWrapper.style.margin = '0 auto';
                                seatsWrapper.style.maxWidth = '800px';
                                seatsWrapper.style.height = '200px';
                                seatsWrapper.style.overflow = 'auto';

                                // Create rows (from row_start letter)
                                const rowStartChar = seatingData.row_start.charCodeAt(0);

                                for (let i = 0; i < seatingData.total_rows; i++) {
                                    const rowLetter = String.fromCharCode(rowStartChar + i);
                                    const rowDiv = document.createElement('div');
                                    rowDiv.className = 'seat-row mb-2';
                                    rowDiv.setAttribute('data-row', rowLetter);

                                    // Create row label
                                    const rowLabel = document.createElement('div');
                                    rowLabel.className = 'row-label me-2 d-inline-block text-center';
                                    rowLabel.style.width = '30px';
                                    rowLabel.style.fontWeight = 'bold';
                                    rowLabel.textContent = rowLetter;
                                    rowDiv.appendChild(rowLabel);

                                    for (let seatNum = 1; seatNum <= seatingData.seats_per_row; seatNum++) {
                                        const seatId = `${rowLetter}${seatNum}`;

                                        // Create seat button
                                        const seatButton = document.createElement('button');
                                        seatButton.className = 'seat btn btn-sm m-1';
                                        seatButton.id = `seat-${seatId}`;
                                        seatButton.setAttribute('data-seat-id', seatId);
                                        seatButton.setAttribute('data-row', rowLetter);
                                        seatButton.setAttribute('data-number', seatNum);

                                        const predefinedSeat = seatingData.rows.find(s => s.rowname === rowLetter);
                                        const seatType = predefinedSeat ? predefinedSeat.type : 'general';

                                        seatButton.setAttribute('data-type', seatType);

                                        // Set appropriate class based on seat type
                                        // getallpackages
                                        if (seatType === 'vip') {
                                            seatButton.classList.add('btn-warning');
                                            seatButton.title = 'VIP Seat';
                                        } else {
                                            seatButton.classList.add('btn-outline-secondary');
                                        }

                                        // Check if seat is blocked
                                        // Check if seat is blocked
                                        const isBlocked = seatingData.blocked_seats &&
                                            seatingData.blocked_seats.some(blockedSeat => {
                                                // Compare both ways: with seatId or with row-letter combination
                                                return blockedSeat.id === seatId ||
                                                    `${blockedSeat.row}-${blockedSeat.seat}` === seatId ||
                                                    `${String.fromCharCode(64 + blockedSeat.row)}${blockedSeat.seat}` === seatId;
                                            });

                                        if (isBlocked) {
                                            seatButton.classList.remove('btn-outline-secondary', 'btn-warning');
                                            seatButton.classList.add('btn-danger');

                                            seatButton.disabled = true;
                                            seatButton.title = 'Blocked';
                                            seatButton.classList.add('cursor-not-allowed');
                                            seatButton.classList.add('text-danger');
                                        } else {
                                            seatButton.addEventListener('click', function() {
                                                toggleSeatSelection(this);
                                            });
                                        }

                                        // Add aisle marker (visual separator)
                                        if (seatingData.aisle_positions && seatingData.aisle_positions.includes(seatNum)) {
                                            const aisleMarker = document.createElement('div');
                                            aisleMarker.className = 'aisle-marker d-inline-block';
                                            aisleMarker.style.width = '20px';
                                            rowDiv.appendChild(aisleMarker);
                                        }

                                        seatButton.textContent = seatNum;
                                        rowDiv.appendChild(seatButton);
                                    }
                                    seatsWrapper.appendChild(rowDiv);
                                }

                                // Add legend
                                const legend = document.createElement('div');
                                legend.className = 'seat-legend mt-4';

                                const packageColors = {
                                    'Standard': 'primary',
                                    'Premium': 'info',
                                    'VIP': 'warning',
                                    'VVIP': 'dark',
                                    'Platinum': 'light'
                                };

                                legend.innerHTML = `
                                <div class="d-flex justify-content-center">
                                    ${getallpackages.map(packageName => {
                                    const color = packageColors[packageName] || 'secondary';
                                    const displayName = packageName || 'Package';
                                    
                                    return `
                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="me-3 mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                <button class="btn btn-sm btn-${color} disabled">■</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                <span> ${displayName}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                            `;
                                }).join('')}
                                    <div class="me-3">
                                        <button class="btn btn-sm btn-danger disabled">■</button>
                                        <span> Blocked</span>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-success disabled">■</button>
                                        <span> Selected</span>
                                    </div>
                                </div>
                                `;

                                container.appendChild(seatsWrapper);
                                container.appendChild(legend);

                                // Add selection counter
                                const counterDiv = document.createElement('div');
                                counterDiv.className = 'selection-counter mt-3 text-center';
                                counterDiv.id = 'selection-counter';
                                counterDiv.innerHTML = `<div class="d-flex justify-content-center align-items-center gap-3">
                                        <strong>Selected: <span id="selected-count">0 / 1</span> seats</strong>
                                        <span class="text-muted">(Max: <span id="max-seats">1</span> seats as per ticket count)</span>
                                    </div>`;
                                container.appendChild(counterDiv);
                            }

                            function handleTicketCountChange() {
                                const ticketCountInput = document.getElementById('ticketCount');
                                const maxSeats = parseInt(ticketCountInput.value) || 1;

                                // Validate min and max
                                if (maxSeats < 1) {
                                    ticketCountInput.value = 1;
                                    toastr.error('Minimum 1 ticket required', 'warning');
                                } else if (maxSeats > 10) {
                                    ticketCountInput.value = 10;
                                    toastr.error('Maximum 10 tickets allowed', 'warning');
                                }

                                // If user reduces ticket count, deselect extra seats
                                if (selectedSeats.length > maxSeats) {
                                    const seatsToRemove = selectedSeats.length - maxSeats;

                                    // Remove the last selected seats
                                    for (let i = 0; i < seatsToRemove; i++) {
                                        const seatId = selectedSeats[selectedSeats.length - 1];
                                        const seatElement = document.getElementById(`seat-${seatId}`);
                                        if (seatElement) {
                                            const seatType = seatElement.getAttribute('data-type');
                                            selectedSeats.pop(); // Remove from array

                                            // Update UI
                                            if (seatType === 'vip') {
                                                seatElement.classList.remove('btn-success');
                                                seatElement.classList.add('btn-warning');
                                            } else {
                                                seatElement.classList.remove('btn-success');
                                                seatElement.classList.add('btn-outline-secondary');
                                            }
                                            seatElement.title = seatType === 'vip' ? 'VIP Seat' : 'General Seat';
                                        }
                                    }

                                    toastr.error(`Reduced selection to ${maxSeats} seat(s). Some seats were deselected.`, 'info');
                                }

                                // Update counter
                                updateSelectionCounter();
                            }

                            // Function to generate stage based on type
                            function generateStage(stageType) {
                                const stage = document.getElementById('stage');
                                stage.className = 'stage'; // Reset classes

                                switch (stageType) {
                                    case "1": // Proscenium/Stage Front
                                        stage.classList.add('stage-proscenium');
                                        stage.style.width = '70%';
                                        stage.style.height = '50px';
                                        stage.style.margin = '0 auto 20px';
                                        stage.style.backgroundColor = '#6c757d';
                                        stage.style.borderRadius = '5px';
                                        break;
                                    case "2": // Round/Runaround Stage
                                        stage.classList.add('stage-round');
                                        stage.style.width = '150px';
                                        stage.style.height = '150px';
                                        stage.style.margin = '0 auto 20px';
                                        stage.style.backgroundColor = '#6c757d';
                                        stage.style.borderRadius = '50%';
                                        break;
                                    case "3": // Thrust Stage
                                        stage.classList.add('stage-thrust');
                                        stage.style.width = '60%';
                                        stage.style.height = '80px';
                                        stage.style.margin = '0 auto 20px';
                                        stage.style.backgroundColor = '#6c757d';
                                        stage.style.borderRadius = '10px 10px 0 0';
                                        stage.style.clipPath = 'polygon(0% 0%, 100% 0%, 100% 100%, 50% 70%, 0% 100%)';
                                        break;
                                    case "4": // Arena/In-the-Round
                                        stage.classList.add('stage-arena');
                                        stage.style.width = '120px';
                                        stage.style.height = '120px';
                                        stage.style.margin = '0 auto 20px';
                                        stage.style.backgroundColor = '#6c757d';
                                        stage.style.borderRadius = '10px';
                                        break;
                                    default:
                                        stage.classList.add('stage-default');
                                        stage.style.width = '80%';
                                        stage.style.height = '40px';
                                        stage.style.margin = '0 auto 20px';
                                        stage.style.backgroundColor = '#6c757d';
                                }
                            }

                            // Add to your existing style block

                            // Function to handle seat selection
                            let selectedSeats = [];

                            function toggleSeatSelection(seatElement) {
                                const seatId = seatElement.getAttribute('data-seat-id');
                                const seatType = seatElement.getAttribute('data-type');

                                const row = seatElement.getAttribute('data-row');
                                const seatNumber = parseInt(seatElement.getAttribute('data-number'));

                                // Check if seat is already selected
                                const index = selectedSeats.indexOf(seatId);

                                const ticketCountInput = document.getElementById('ticketCount');
                                const maxSeats = parseInt(ticketCountInput.value) || 1;

                                if (index === -1) {
                                    // Check if user has reached the maximum allowed seats
                                    if (selectedSeats.length >= maxSeats) {
                                        toastr.error(`You can only select ${maxSeats} seat(s) as per your ticket count.`, 'warning');
                                        return;
                                    }

                                    // If user wants multiple seats (group booking)
                                    if (maxSeats > 1 && selectedSeats.length === 0) {
                                        // Find adjacent available seats in the same row
                                        const adjacentSeats = findBestAdjacentSeats(row, seatNumber,
                                            maxSeats); //findAdjacentSeats(row, seatNumber, maxSeats);

                                        if (adjacentSeats.length === maxSeats) {
                                            // Select all adjacent seats
                                            adjacentSeats.forEach(adjSeatId => {
                                                const adjSeatElement = document.getElementById(`seat-${adjSeatId}`);
                                                if (adjSeatElement && !adjSeatElement.disabled && !selectedSeats.includes(adjSeatId)) {
                                                    selectSeat(adjSeatElement);
                                                }
                                            });
                                            if (adjacentSeats.length < maxSeats) {
                                                toastr.info(
                                                    `Only found ${adjacentSeats.length} adjacent seats. You can select additional seats manually.`,
                                                    'info');
                                            }
                                        } else if (adjacentSeats.length > 1) {
                                            // Found some adjacent seats but not all requested
                                            adjacentSeats.forEach(adjSeatId => {
                                                const adjSeatElement = document.getElementById(`seat-${adjSeatId}`);
                                                if (adjSeatElement && !adjSeatElement.disabled && !selectedSeats.includes(adjSeatId)) {
                                                    selectSeat(adjSeatElement);
                                                }
                                            });
                                            toastr.info(
                                                `Found ${adjacentSeats.length} adjacent seats. Select ${maxSeats - adjacentSeats.length} more seat(s) manually.`,
                                                'info');
                                        } else {
                                            // Not enough adjacent seats, just select the clicked one
                                            // toastr.error(`Not enough adjacent seats available. Please select seats individually.`, 'info');
                                            selectSeat(seatElement);
                                        }
                                    } else {
                                        // Single seat selection or adding to existing selection
                                        selectSeat(seatElement);
                                    }
                                } else {
                                    // Deselect seat
                                    deselectSeat(seatElement, seatType);
                                }


                                // if (index === -1) {
                                //     // Select seat
                                //     selectedSeats.push(seatId);
                                //     seatElement.classList.remove('btn-outline-secondary', 'btn-warning');
                                //     seatElement.classList.add('btn-success');
                                //     seatElement.title = `Selected: ${seatId}`;
                                // } else {
                                //     // Deselect seat
                                //     selectedSeats.splice(index, 1);
                                //     if (seatType === 'vip') {
                                //         seatElement.classList.remove('btn-success');
                                //         seatElement.classList.add('btn-warning');
                                //     } else {
                                //         seatElement.classList.remove('btn-success');
                                //         seatElement.classList.add('btn-outline-secondary');
                                //     }
                                //     seatElement.title = seatType === 'vip' ? 'VIP Seat' : 'General Seat';
                                // }

                                // Update counter
                                updateSelectionCounter();
                            }

                            function findBestAdjacentSeats(row, startSeatNumber, count) {
                                const allPossibleSeats = [];

                                // Try to find seats in this priority:
                                // 1. All to the right (A1, A2, A3, A4)
                                // 2. All to the left (A1, A0, A-1, A-2) - but limited
                                // 3. Mixed left and right (A0, A1, A2, A3)
                                // 4. As many adjacent as possible

                                // Get maximum seat number in this row
                                const maxSeats = 20; // You should get this from seatingData.seats_per_row

                                // Strategy 1: Try to get seats to the right first
                                const rightSeats = [];
                                for (let i = 0; i < count; i++) {
                                    const seatNumber = startSeatNumber + i;
                                    if (seatNumber > maxSeats) break;

                                    const seatId = `${row}${seatNumber}`;
                                    const seatElement = document.getElementById(`seat-${seatId}`);

                                    if (seatElement && !seatElement.disabled && !selectedSeats.includes(seatId)) {
                                        // Check aisle crossing
                                        if (i > 0 && isAislePosition(seatNumber - 1)) {
                                            break;
                                        }
                                        rightSeats.push(seatId);
                                    } else {
                                        break;
                                    }
                                }

                                if (rightSeats.length === count) {
                                    return rightSeats;
                                }

                                // Strategy 2: Try to get seats including left side
                                const leftSeats = [];
                                const neededFromLeft = count - rightSeats.length;

                                for (let i = 1; i <= neededFromLeft; i++) {
                                    const seatNumber = startSeatNumber - i;
                                    if (seatNumber < 1) break;

                                    const seatId = `${row}${seatNumber}`;
                                    const seatElement = document.getElementById(`seat-${seatId}`);

                                    if (seatElement && !seatElement.disabled && !selectedSeats.includes(seatId)) {
                                        // Check aisle crossing
                                        if (isAislePosition(seatNumber)) {
                                            break;
                                        }
                                        leftSeats.unshift(seatId); // Add to beginning
                                    } else {
                                        break;
                                    }
                                }

                                const combinedSeats = [...leftSeats, ...rightSeats];
                                if (combinedSeats.length === count) {
                                    return combinedSeats;
                                }

                                // Strategy 3: Return as many as possible
                                if (combinedSeats.length > 0) {
                                    return combinedSeats;
                                }

                                // Strategy 4: Just return the clicked seat
                                return [`${row}${startSeatNumber}`];
                            }

                            function selectSeat(seatElement) {
                                const seatId = seatElement.getAttribute('data-seat-id');
                                const seatType = seatElement.getAttribute('data-type');

                                selectedSeats.push(seatId);
                                seatElement.classList.remove('btn-outline-secondary', 'btn-warning');
                                seatElement.classList.add('btn-success');
                                seatElement.title = `Selected: ${seatId}`;
                            }

                            function deselectSeat(seatElement, seatType) {
                                const seatId = seatElement.getAttribute('data-seat-id');
                                const index = selectedSeats.indexOf(seatId);

                                if (index !== -1) {
                                    selectedSeats.splice(index, 1);
                                    if (seatType === 'vip') {
                                        seatElement.classList.remove('btn-success');
                                        seatElement.classList.add('btn-warning');
                                    } else {
                                        seatElement.classList.remove('btn-success');
                                        seatElement.classList.add('btn-outline-secondary');
                                    }
                                    seatElement.title = seatType === 'vip' ? 'VIP Seat' : 'General Seat';
                                }
                            }

                            function findAdjacentSeats(row, startSeatNumber, count) {
                                const adjacentSeats = [];

                                // First, check how many consecutive seats are available to the right
                                let availableToRight = 0;
                                for (let i = 0; i < count; i++) {
                                    const seatNumber = startSeatNumber + i;
                                    const seatId = `${row}${seatNumber}`;
                                    const seatElement = document.getElementById(`seat-${seatId}`);

                                    // Check if seat exists and is available
                                    if (!seatElement || seatElement.disabled || selectedSeats.includes(seatId)) {
                                        break;
                                    }

                                    // Don't cross aisles
                                    if (i > 0 && isAislePosition(seatNumber - 1)) {
                                        break;
                                    }

                                    availableToRight++;
                                }

                                // Check how many consecutive seats are available to the left (excluding the clicked seat)
                                let availableToLeft = 0;
                                for (let i = 1; i <= count - availableToRight; i++) {
                                    const seatNumber = startSeatNumber - i;
                                    const seatId = `${row}${seatNumber}`;
                                    const seatElement = document.getElementById(`seat-${seatId}`);

                                    // Check if seat exists and is available
                                    if (!seatElement || seatElement.disabled || selectedSeats.includes(seatId)) {
                                        break;
                                    }

                                    // Don't cross aisles
                                    if (isAislePosition(seatNumber)) {
                                        break;
                                    }

                                    availableToLeft++;
                                }

                                // Calculate total available adjacent seats
                                const totalAvailable = 1 + availableToRight + availableToLeft; // +1 for the clicked seat

                                if (totalAvailable >= count) {
                                    // We have enough adjacent seats
                                    // Build the seat array from left to right
                                    for (let i = availableToLeft; i > 0; i--) {
                                        const seatNumber = startSeatNumber - i;
                                        adjacentSeats.push(`${row}${seatNumber}`);
                                    }

                                    // Add the clicked seat
                                    adjacentSeats.push(`${row}${startSeatNumber}`);

                                    // Add seats to the right
                                    for (let i = 1; i <= availableToRight; i++) {
                                        const seatNumber = startSeatNumber + i;
                                        adjacentSeats.push(`${row}${seatNumber}`);
                                    }

                                    // Return only the number of seats requested
                                    return adjacentSeats.slice(0, count);
                                }

                                // Not enough adjacent seats
                                return [];
                            }

                            // Helper function to find adjacent seats to the left
                            function findAdjacentSeatsLeft(row, startSeatNumber, count) {
                                const adjacentSeats = [];

                                // Check seats to the left
                                for (let i = 0; i < count; i++) {
                                    const seatNumber = startSeatNumber - i;
                                    const seatId = `${row}${seatNumber}`;
                                    const seatElement = document.getElementById(`seat-${seatId}`);

                                    if (!seatElement || seatElement.disabled || selectedSeats.includes(seatId)) {
                                        // Not enough seats available
                                        return [];
                                    }

                                    // Check for aisle positions - don't select across aisles
                                    if (i > 0 && isAislePosition(seatNumber + 1)) {
                                        // Next seat was an aisle, can't select across aisle
                                        return [];
                                    }

                                    adjacentSeats.unshift(seatId); // Add to beginning to maintain order
                                }

                                return adjacentSeats;
                            }

                            // Function to check if a seat position is an aisle
                            function isAislePosition(seatNumber) {
                                // Get seating data to check aisle positions
                                const seatingData = getSeatingData();
                                if (seatingData && seatingData.aisle_positions) {
                                    return seatingData.aisle_positions.includes(seatNumber);
                                }
                                return false;
                            }

                            function checkSeatAvailability(row, seatNumbers) {
                                const unavailableSeats = [];
                                const availableSeats = [];

                                seatNumbers.forEach(seatNumber => {
                                    const seatId = `${row}${seatNumber}`;
                                    const seatElement = document.getElementById(`seat-${seatId}`);

                                    if (!seatElement || seatElement.disabled || selectedSeats.includes(seatId)) {
                                        unavailableSeats.push(seatId);
                                    } else {
                                        availableSeats.push(seatId);
                                    }
                                });

                                return {
                                    available: availableSeats,
                                    unavailable: unavailableSeats,
                                    allAvailable: unavailableSeats.length === 0
                                };
                            }

                            function suggestSeatGroups(row, startSeatNumber, count) {
                                const suggestions = [];
                                const maxSeats = 20; // Get from seating data

                                // Try different starting positions
                                for (let offset = -3; offset <= 3; offset++) {
                                    const start = startSeatNumber + offset;
                                    if (start < 1 || start > maxSeats) continue;

                                    const seats = [];
                                    let isValid = true;

                                    for (let i = 0; i < count; i++) {
                                        const seatNumber = start + i;
                                        if (seatNumber > maxSeats) {
                                            isValid = false;
                                            break;
                                        }

                                        const seatId = `${row}${seatNumber}`;
                                        const seatElement = document.getElementById(`seat-${seatId}`);

                                        // Check if seat is available and not crossing aisles
                                        if (!seatElement || seatElement.disabled || selectedSeats.includes(seatId) ||
                                            (i > 0 && isAislePosition(seatNumber - 1))) {
                                            isValid = false;
                                            break;
                                        }

                                        seats.push(seatId);
                                    }

                                    if (isValid && seats.length === count) {
                                        suggestions.push(seats);
                                    }
                                }

                                return suggestions;
                            }
                            // Update selection counter
                            // function updateSelectionCounter() {
                            //     const counter = document.getElementById('selected-count');
                            //     if (counter) {
                            //         counter.textContent = selectedSeats.length;
                            //     }
                            // }
                            function updateSelectionCounter() {
                                const counter = document.getElementById('selected-count');
                                const ticketCountInput = document.getElementById('ticketCount');
                                const maxSeats = parseInt(ticketCountInput.value) || 1;

                                if (counter) {
                                    counter.textContent = `${selectedSeats.length} / ${maxSeats}`;
                                    if (selectedSeats.length >= maxSeats) {
                                        counter.style.color = 'green';
                                    } else {
                                        counter.style.color = '';
                                    }
                                }
                            }
                            // Function to get selected seats (for form submission)
                            function getSelectedSeats() {
                                return selectedSeats;
                            }

                            // Function to clear seat selection
                            function clearSeatSelection() {
                                selectedSeats = [];
                                const seatButtons = document.querySelectorAll('.seat:not(:disabled)');
                                seatButtons.forEach(button => {
                                    const seatType = button.getAttribute('data-type');
                                    button.classList.remove('btn-success');
                                    if (seatType === 'vip') {
                                        button.classList.add('btn-warning');
                                    } else {
                                        button.classList.add('btn-outline-secondary');
                                    }
                                });
                                updateSelectionCounter();
                            }

                            // Call this function when date is selected
                            function onDateChange() {
                                // Get packages first (from your existing getpackages() function)
                                if (typeof getpackages === 'function') {
                                    getpackages();
                                }

                                // Then generate seating layout
                                setTimeout(() => {
                                    generateSeatingLayout();
                                }, 100); // Small delay to ensure DOM is updated
                            }

                            // Initialize when page loads
                            document.addEventListener('DOMContentLoaded', function() {
                                // Modify your existing select to call onDateChange
                                const dateSelect = document.getElementById('venue_dates');
                                if (dateSelect) {
                                    dateSelect.onchange = onDateChange;
                                }
                                const ticketCountInput = document.getElementById('ticketCount');
                                if (ticketCountInput) {
                                    ticketCountInput.addEventListener('change', handleTicketCountChange);
                                    ticketCountInput.addEventListener('input', handleTicketCountChange);
                                }
                                // Initial call if a date is already selected
                                if (dateSelect && dateSelect.value) {
                                    setTimeout(() => {
                                        generateSeatingLayout();
                                    }, 500);
                                }
                            });

                            // Add CSS styles for better appearance
                            const style = document.createElement('style');
                            style.textContent = `
                                    .seat {
                                        width: 35px;
                                        height: 35px;
                                        padding: 0;
                                        font-size: 12px;
                                        transition: all 0.2s;
                                    }
                                    
                                    .seat:hover:not(:disabled) {
                                        transform: scale(1.1);
                                    }
                                    
                                    .seat-row {
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                    }
                                    
                                    .row-label {
                                        font-size: 14px;
                                        color: #495057;
                                    }
                                    
                                    .stage {
                                        color: white;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        font-weight: bold;
                                    }
                                    
                                    .stage-label {
                                        font-weight: bold;
                                        color: #495057;
                                        margin-top: 5px;
                                    }
                                    
                                    .seat-legend {
                                        background-color: #f8f9fa;
                                        padding: 10px;
                                        border-radius: 5px;
                                        border: 1px solid #dee2e6;
                                    }
                                    
                                    .selection-counter {
                                        padding: 10px;
                                        background-color: #e9ecef;
                                        border-radius: 5px;
                                    }
                                    
                                    .aisle-marker {
                                        width: 20px;
                                        display: inline-block;
                                    }
                                `;

                            document.head.appendChild(style);
                        </script>
                        <div class="row">

                            <div class="col-md-4 form-group">
                                <label class="w-100"></label>
                                <button id="generateTickets" class="btn btn-info">Generate Ticket Forms</button>
                            </div>
                        </div>
                        <div class="card ticket-form mt-3" id="ticketFormSection">
                            <div class="card-body">
                                <h2 class="card-title">Attendee Information</h2>
                                <div id="ticketFormsContainer" style="height: 200px;overflow:auto"></div>
                                <div class="summary">
                                    <h3>Order Summary</h3>
                                    <div class="summary-item">
                                        <span>Number of Tickets:</span>
                                        <span id="summaryCount">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span>Price per Ticket:</span>
                                        <span id="summaryPrice">$0.00</span>
                                    </div>
                                    <div class="summary-total">
                                        <span>Total Amount:</span>
                                        <span id="summaryTotal">$0.00</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button id="payNow" class="btn btn-success w-100" style="margin-top: 20px;">Pay
                                            Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/country-picker-init.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>

    <script>
        function getEvents() {
            let event_id = $('#event_ids').val();
            $.ajax({
                url: "{{ route('event-vendor.pos.get-venue-list') }}",
                type: 'POST',
                data: {
                    event_id
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(res) {
                    $('#venue_ids').empty();
                    $(`#venue_ids`).append(`<option value="">Select Venue</option>`);
                    $.each(res.data, function(key, val) {
                        $(`#venue_ids`).append(
                            `<option value="${val.id}" data-time='${JSON.stringify(val.date).replace(/'/g, "&apos;")}'>${val.name}</option>`
                        );
                    });
                    $('#venue_dates').empty();
                    $('#venue_packages').empty();
                },
                error: function(error) {
                    console.error('Error in form submission:', error);
                }
            });
        }

        function getVenues() {
            let event_id = $('#venue_ids option:selected').data('time');
            $('#venue_dates').empty();
            $(`#venue_dates`).append(`<option value="">Select date</option>`);
            $.each(event_id, function(key, val) {
                $(`#venue_dates`).append(
                    `<option value="${val.id}" data-package='${JSON.stringify(val.package).replace(/'/g, "&apos;")}'  data-seats='${JSON.stringify(val.seats).replace(/'/g, "&apos;")}'>${val.date}</option>`
                );
            });
        }
        const getallpackages = [];

        function getpackages() {
            let venue = $('#venue_dates option:selected').data('package');
            $('#venue_packages').empty();
            $(`#venue_packages`).append(`<option value="">Select date</option>`);
            getallpackages.length = 0;
            $.each(venue, function(key, val) {
                $(`#venue_packages`).append(
                    `<option value="${val.package_id}" data-seats_no="${val.seats_no}" data-price_no="${val.price_no}" data-available="${val.available}" data-sold="${val.sold}">${val.package_name}</option>`
                );
                if (!getallpackages.includes(val.package_name)) {
                    getallpackages.push(val.package_name);
                }
            });
        }

        function getsponsors() {
            $('#sponsor_ids').empty();
            $('.sponsor-data').addClass('d-none');

            let type = $('#types').val();
            let event_id = $('#event_ids').val();
            let package_id = $('#venue_packages').val();
            if (!event_id) {
                toastr.error('Please select a Event');
                return false;
            }
            if (!package_id) {
                toastr.error('Please select a Packages');
                return false;
            }
            if (type == 'sponsor' || type == 'complimentary') {
                $.ajax({
                    url: "{{ route('event-vendor.pos.get-sponsor-list') }}",
                    type: 'POST',
                    data: {
                        type,
                        event_id,
                        package_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        $('.sponsor-data').removeClass('d-none');
                        $('#sponsor_ids').empty();
                        $(`#sponsor_ids`).append(`<option value="">Select Sponsors</option>`);
                        $.each(res.data, function(key, val) {
                            $(`#sponsor_ids`).append(`<option value="${val.id}">${val.name}</option>`);
                        });
                    },
                    error: function(error) {
                        $('.sponsor-data').addClass('d-none');
                        console.error('Error in form submission:', error);
                    }
                });
            }

        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const eventName = document.getElementById('event_ids');
            const package = document.getElementById('venue_packages');
            const venue = document.getElementById('venue_ids');
            const userType = document.getElementById('types');
            const eventDate = document.getElementById('venue_dates');
            const ticketCount = document.getElementById('ticketCount');
            const generateTicketsBtn = document.getElementById('generateTickets');
            const ticketFormSection = document.getElementById('ticketFormSection');
            const ticketFormsContainer = document.getElementById('ticketFormsContainer');
            const payNowBtn = document.getElementById('payNow');
            const ticketPreview = document.getElementById('ticketPreview');

            // Set default date to today
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            eventDate.value = formattedDate;

            // Price mapping based on package
            const priceMap = {
                'vip': 150,
                'premium': 100,
                'general': 50,
                'student': 25
            };

            // Generate ticket forms when button is clicked
            generateTicketsBtn.addEventListener('click', function() {
                // Validate form
                if (!eventName.value || !package.value || !venue.value || !userType.value || !eventDate
                    .value) {
                    toastr.error('Please fill in all event details');
                    return false;
                }

                const count = Number(ticketCount.value);
                if (count < 1) {
                    toastr.error('Please enter a valid number of tickets');
                    return;
                }

                ticketFormsContainer.innerHTML = '';

                // Generate forms for each ticket
                for (let i = 1; i <= count; i++) {
                    const ticketRow = document.createElement('div');
                    ticketRow.className = 'ticket-row';
                    ticketRow.innerHTML = `
                        <div class="ticket-header">
                            <div class="ticket-number">Ticket #${i}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                    <label for="userName${i}">Full Name</label>
                                    <input type="text" id="userName${i}" placeholder="Enter attendee name" value="devotees${i}" class="form-control">
                                
                            </div>
                            <div class="col-md-4 form-group">
                                    <label for="userPhone${i}">Phone Number</label>
                                    <input type="tel" id="userPhone${i}" placeholder="Enter phone number"  value="+910000000000" maxlength="16" class="form-control">
                               
                            </div>
                            <div class="col-md-4 form-group">
                                    <label for="userEmail${i}">Aadhar Number</label>
                                    <input type="number" id="useraadhar${i}" placeholder="Enter Aadhar Number" class="form-control" value="000000000000">
                            </div>
                        </div>                        
                    `;

                    ticketFormsContainer.appendChild(ticketRow);

                    // Add event listener to remove button if it exists
                    const removeBtn = ticketRow.querySelector('.remove-ticket');
                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            ticketRow.remove();
                            updateSummary();
                        });
                    }
                }

                // Show the ticket form section
                ticketFormSection.style.display = 'block';

                // Update summary
                updateSummary();

                // Scroll to ticket forms
                ticketFormSection.scrollIntoView({
                    behavior: 'smooth'
                });
            });

            // Update summary when ticket count changes
            ticketCount.addEventListener('change', updateSummary);
            package.addEventListener('change', updateSummary);

            // Pay Now button
            payNowBtn.addEventListener('click', function() {
                // Validate all ticket forms
                const count = parseInt(ticketCount.value);
                let isValid = true;

                for (let i = 1; i <= count; i++) {
                    const name = document.getElementById(`userName${i}`);
                    const phone = document.getElementById(`userPhone${i}`);
                    const email = document.getElementById(`userEmail${i}`);

                    // Check if the form exists (might have been removed)
                    if (name && phone && email) {
                        if (!name.value || !phone.value || !email.value) {
                            isValid = false;
                            // Highlight empty fields
                            if (!name.value) name.style.borderColor = 'red';
                            if (!phone.value) phone.style.borderColor = 'red';
                            if (!email.value) email.style.borderColor = 'red';
                        } else {
                            // Reset border color if valid
                            name.style.borderColor = '#ddd';
                            phone.style.borderColor = '#ddd';
                            email.style.borderColor = '#ddd';
                        }
                    }
                }

                if (!isValid) {
                    alert('Please fill in all attendee information');
                    return;
                }

                // Show ticket preview
                updateTicketPreview();
                ticketPreview.style.display = 'block';

                // Scroll to preview
                ticketPreview.scrollIntoView({
                    behavior: 'smooth'
                });

                // In a real application, you would redirect to payment gateway here
                // For demo purposes, we'll just show a success message
                setTimeout(function() {
                    alert(
                        'Payment successful! Your tickets have been generated and will be emailed to you.'
                    );
                }, 1000);
            });

            // Function to update the order summary
            function updateSummary() {
                const count = parseInt(ticketCount.value);
                const packageValue = package.value;
                const price = $('#venue_packages option:selected').data('price_no') || 0;
                const total = count * price;

                document.getElementById('summaryCount').textContent = count;
                document.getElementById('summaryPrice').textContent = `$${price.toFixed(2)}`;
                document.getElementById('summaryTotal').textContent = `$${total.toFixed(2)}`;
            }

            // Function to update the ticket preview
            function updateTicketPreview() {
                document.getElementById('previewEvent').textContent = eventName.options[eventName.selectedIndex]
                    .text;
                document.getElementById('previewPackage').textContent = package.options[package.selectedIndex].text;
                document.getElementById('previewVenue').textContent = venue.options[venue.selectedIndex].text;
                document.getElementById('previewDate').textContent = eventDate.value;

                // Use the first attendee for preview
                const firstName = document.getElementById('userName1').value;
                const firstPhone = document.getElementById('userPhone1').value;

                document.getElementById('previewAttendee').textContent = firstName;
                document.getElementById('previewPhone').textContent = firstPhone;
                document.getElementById('previewId').textContent = `TKT-${Math.floor(1000 + Math.random() * 9000)}`;
            }
        });
    </script>
@endpush
