@extends('layouts.app')

@section('title', 'About Us - Horror-Bark Theme Park')

@section('content')
    <section class="mb-12">
        <x-ui.section-heading title="The Genesis of Fear" size="lg" class="mb-6" />
        <p class="text-gray-300 leading-relaxed mb-4">
            Horror-Bark Theme Park began with a chilling vision: to create an immersive world where the darkest imaginations could take form. Founded in 2019, by a collective of thrill-seekers and horror aficionados, our journey started with a single haunted attraction.
        </p>
        <p class="text-gray-300 leading-relaxed mb-4">
            Driven by a passion for the macabre and a desire to push the boundaries of themed entertainment, we meticulously crafted each scare, each story, and each environment.
        </p>
        <p class="text-gray-300 leading-relaxed">
            Our commitment remains steadfast: to deliver unforgettable, spine-tingling experiences that linger long after the visit.
        </p>
    </section>

    <section class="mb-12">
        <x-ui.section-heading title="Meet the Minds Behind the Mayhem" size="lg" class="mb-6" />
        <x-about.team-grid :members="[
            ['name' => 'Evelyn Thorne', 'role' => 'Visionary Founder', 'image' => 'https://ui-avatars.com/api/?name=Evelyn+Thorne&size=256&background=7c3aed&color=fff&bold=true'],
            ['name' => 'Silas Blackwood', 'role' => 'Creative Director', 'image' => 'https://ui-avatars.com/api/?name=Silas+Blackwood&size=256&background=dc2626&color=fff&bold=true'],
            ['name' => 'Jasper Crowe', 'role' => 'Lead Engineer', 'image' => 'https://ui-avatars.com/api/?name=Jasper+Crowe&size=256&background=0f766e&color=fff&bold=true'],
        ]" />
        <p class="mt-4 text-gray-300">Our dedicated team of designers, storytellers, and engineers work tirelessly to bring your darkest dreams to life.</p>
    </section>

    <section class="mb-12">
        <x-ui.section-heading title="The Fear We Cultivate" size="lg" class="mb-6" />
        <p class="text-gray-300 leading-relaxed mb-4">
            At Horror-Bark, we believe that fear is a powerful emotion, capable of evoking both terror and exhilaration.
        </p>
        <p class="text-gray-300 leading-relaxed">
            Our commitment to safety is paramount. While we aim to thrill, we ensure that every attraction and experience is designed and maintained with the highest safety standards.
        </p>
    </section>

    <section>
        <x-ui.section-heading title="Connect with the Unseen" size="lg" class="mb-6" />
        <p class="text-gray-300 leading-relaxed mb-4">Have questions or need to get in touch with the shadows? Reach out to our team.</p>
        <x-about.contact-info
            email="info@horror-bark.com"
            phone="+960 999999"
            location="4.2260552262693,73.426287174225"
        />
    </section>
@endsection
