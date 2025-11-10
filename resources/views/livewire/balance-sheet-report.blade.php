<div style="background: yellow; padding: 50px; margin: 20px;">
    <h1 style="font-size: 48px; color: red;">BALANCE SHEET - TEST</h1>
    
    @php
    echo "<p style='font-size: 32px; color: blue;'>PHP Working!</p>";
    
    try {
        $count = \App\Models\MonthlyClosing::count();
        echo "<p style='font-size: 28px; color: green;'>Total Data: " . $count . "</p>";
        
        $all = \App\Models\MonthlyClosing::all();
        foreach ($all as $row) {
            echo "<div style='background: white; margin: 10px; padding: 15px; border: 3px solid black;'>";
            echo "<strong>ID:</strong> " . $row->monthly_closing_id . " | ";
            echo "<strong>Year:</strong> " . $row->closing_year . " | ";
            echo "<strong>Month:</strong> " . $row->closing_month . "<br>";
            echo "<strong>Saldo Awal:</strong> " . number_format($row->saldo_awal, 2);
            echo "</div>";
        }
        
    } catch (\Exception $e) {
        echo "<p style='font-size: 28px; color: red; background: white; padding: 20px;'>ERROR: " . $e->getMessage() . "</p>";
    }
    @endphp
</div>
