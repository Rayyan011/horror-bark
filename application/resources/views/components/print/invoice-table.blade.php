@props([
    'rows' => [],
])

<table style="width: 100%; border-collapse: collapse; margin-top: 12px;">
    @foreach ($rows as $row)
        <tr>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background: #f5f5f5;">{{ $row['label'] }}</th>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ $row['value'] }}</td>
        </tr>
    @endforeach
</table>
