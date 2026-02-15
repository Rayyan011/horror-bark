@props([
    'email',
    'phone',
    'location',
])

<ul class="list-disc list-inside text-gray-700">
    <li>Email: <a href="mailto:{{ $email }}" class="text-red-600 hover:underline">{{ $email }}</a></li>
    <li>Phone: {{ $phone }}</li>
    <li>Location: {{ $location }}</li>
</ul>
