<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Debug Gacha</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>
    <div style="padding: 20px;">
        <h1>Debug Gacha Page</h1>
        
        <div id="debug-info">
            <h3>Debug Information:</h3>
            <div id="debug-content">Loading...</div>
        </div>
        
        <button onclick="testDraw()" class="btn btn-primary">Test Draw</button>
        <div id="display-text" style="font-size: 2rem; margin: 20px 0;">SIAP?</div>
    </div>

    <script>
        // Debug data
        let employees = [];
        let prizes = [];
        let selectedPrizeName = "Doorprize";
        let selectedPlant = "all";
        
        // Load data safely
        try {
            @php
                $employees = \App\Models\Employee::with('plant')->get();
                $prizes = \App\Models\Prize::all();
                $nama_hadiah_manual = request('hadiah', 'Doorprize');
                $selected_plant = request('plant_id', 'all');
            @endphp
            
            employees = @json($employees);
            prizes = @json($prizes);
            selectedPrizeName = "{{ $nama_hadiah_manual }}";
            selectedPlant = "{{ $selected_plant }}";
            
            console.log('Data loaded successfully:', {
                employees: employees.length,
                prizes: prizes.length,
                selectedPrizeName: selectedPrizeName,
                selectedPlant: selectedPlant
            });
            
            // Update debug info
            document.getElementById('debug-content').innerHTML = `
                <p><strong>Employees:</strong> ${employees.length}</p>
                <p><strong>Prizes:</strong> ${prizes.length}</p>
                <p><strong>Selected Prize:</strong> ${selectedPrizeName}</p>
                <p><strong>Selected Plant:</strong> ${selectedPlant}</p>
                <p><strong>First Employee:</strong> ${employees[0]?.employee_name || 'None'}</p>
                <p><strong>First Prize:</strong> ${prizes[0]?.nama_hadiah || 'None'}</p>
            `;
            
        } catch (error) {
            console.error('Error loading data:', error);
            document.getElementById('debug-content').innerHTML = `
                <p style="color: red;"><strong>Error:</strong> ${error.message}</p>
            `;
        }
        
        function testDraw() {
            console.log('Test draw clicked');
            
            if (!employees || employees.length === 0) {
                alert('No employees available!');
                return;
            }
            
            // Simple shuffle
            const display = document.getElementById('display-text');
            let count = 0;
            const maxCount = 10;
            
            const interval = setInterval(() => {
                const random = employees[Math.floor(Math.random() * employees.length)];
                display.innerText = random.employee_name;
                count++;
                
                if (count >= maxCount) {
                    clearInterval(interval);
                    display.innerText = 'DONE!';
                }
            }, 200);
        }
    </script>
</body>
</html>
