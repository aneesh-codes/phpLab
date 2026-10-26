<!-- Write a PHP program to calculate the annual income tax of an individual according to the
specified Nepal Government income-tax rates for FY 2083/84. Create an HTML form to
accept the annual taxable income and apply the progressive tax slabs specified in the lab
question. Display the annual taxable income, tax calculated under each applicable slab,
total tax payable, and net income after tax. -->

<?php
$income = isset($_POST['income']) ? (float) $_POST['income'] : 0;
$taxBreakdown = [];
$totalTax = 0;
$netIncome = 0;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($income < 0 || !is_numeric($_POST['income'])) {
        $error = 'Please enter a valid non-negative annual taxable income.';
    } else {
        $slabs = [
            ['lower' => 0, 'upper' => 500000, 'rate' => 0],
            ['lower' => 500000, 'upper' => 700000, 'rate' => 0.01],
            ['lower' => 700000, 'upper' => 1000000, 'rate' => 0.10],
            ['lower' => 1000000, 'upper' => 2000000, 'rate' => 0.20],
            ['lower' => 2000000, 'upper' => PHP_FLOAT_MAX, 'rate' => 0.30]
        ];

        foreach ($slabs as $slab) {
            $taxableAmount = max(0, min($income, $slab['upper']) - $slab['lower']);
            if ($taxableAmount > 0) {
                $tax = $taxableAmount * $slab['rate'];
                $taxBreakdown[] = [
                    'range' => ($slab['lower'] == 0 ? '0' : number_format($slab['lower'])) . ' - ' . ($slab['upper'] == PHP_FLOAT_MAX ? 'Above' : number_format($slab['upper'])),
                    'taxable_amount' => $taxableAmount,
                    'rate' => $slab['rate'] * 100,
                    'tax' => $tax
                ];
                $totalTax += $tax;
            }
        }

        $netIncome = $income - $totalTax;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Tax Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        form {
            margin-bottom: 25px;
        }
        input {
            padding: 8px;
            width: 250px;
            margin-top: 5px;
        }
        table {
            border-collapse: collapse;
            width: 700px;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f2f2f2;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Annual Income Tax Calculator (FY 2083/84)</h2>

    <form method="post" action="">
        <label for="income">Annual Taxable Income (NPR):</label><br>
        <input type="number" name="income" id="income" value="<?php echo htmlspecialchars($income); ?>" step="0.01" min="0" required>
        <br><br>
        <input type="submit" value="Calculate Tax">
    </form>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($error)): ?>
        <h3>Tax Calculation Result</h3>
        <p><strong>Annual Taxable Income:</strong> NPR <?php echo number_format($income, 2); ?></p>

        <table>
            <tr>
                <th>Tax Slab</th>
                <th>Taxable Amount</th>
                <th>Rate</th>
                <th>Tax</th>
            </tr>

            <?php foreach ($taxBreakdown as $row): ?>
                <tr>
                    <td><?php echo $row['range']; ?></td>
                    <td>NPR <?php echo number_format($row['taxable_amount'], 2); ?></td>
                    <td><?php echo number_format($row['rate'], 2); ?>%</td>
                    <td>NPR <?php echo number_format($row['tax'], 2); ?></td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <th>Total Tax Payable</th>
                <td colspan="3"><strong>NPR <?php echo number_format($totalTax, 2); ?></strong></td>
            </tr>
            <tr>
                <th>Net Income After Tax</th>
                <td colspan="3"><strong>NPR <?php echo number_format($netIncome, 2); ?></strong></td>
            </tr>
        </table>
    <?php endif; ?>
</body>
</html>


