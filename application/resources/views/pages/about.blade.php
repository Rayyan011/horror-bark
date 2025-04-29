@extends('layouts.app')

@section('title', 'About Us - Horror-Bark Theme Park') {{-- Optional: Set page title --}}

@section('content') {{-- Start the content section --}}
    <section class="bg-cover bg-center h-72" style="background-image: url('{{ asset('images/about-hero.jpg') }}');">
        <div class="bg-black bg-opacity-60 h-full flex items-center justify-center">
            <div class="text-center text-white">
                <h2 class="text-4xl font-bold mb-4 horror-font">Our Story at Horror-Bark</h2>
                <p class="text-lg">Unveiling the mysteries behind the thrills.</p>
            </div>
        </div>
    </section>

    <main class="container mx-auto my-8 px-4">
        <section class="mb-12">
            <h2 class="text-3xl font-bold mb-6 horror-font">The Genesis of Fear</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Horror-Bark Theme Park began with a chilling vision: to create an immersive world where the darkest imaginations could take form. Founded in [Year], by a collective of thrill-seekers and horror aficionados, our journey started with a single haunted attraction.
            </p>
            <p class="text-gray-700 leading-relaxed mb-4">
                Driven by a passion for the macabre and a desire to push the boundaries of themed entertainment, we meticulously crafted each scare, each story, and each environment. Over the years, Horror-Bark has evolved, adding new layers of terror and excitement, becoming the premier destination for those who crave the extraordinary and the unsettling.
            </p>
            <p class="text-gray-700 leading-relaxed">
                Our commitment remains steadfast: to deliver unforgettable, spine-tingling experiences that linger long after the visit. We believe in the power of immersive storytelling and the thrill of the unexpected.
            </p>
        </section>

        <section class="mb-12">
            <h2 class="text-3xl font-bold mb-6 horror-font">Meet the Minds Behind the Mayhem</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gray-800 p-6 rounded shadow border border-gray-700 text-center">
                    <img src="{{ asset('images/team/placeholder.jpg') }}" alt="Founder 1" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-300 mb-1 horror-font">Evelyn Thorne</h3>
                    <p class="text-gray-500 text-sm">Visionary Founder</p>
                </div>
                <div class="bg-gray-800 p-6 rounded shadow border border-gray-700 text-center">
                    <img src="{{ asset('images/team/placeholder.jpg') }}" alt="Creative Director" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-300 mb-1 horror-font">Silas Blackwood</h3>
                    <p class="text-gray-500 text-sm">Creative Director</p>
                </div>
                <div class="bg-gray-800 p-6 rounded shadow border border-gray-700 text-center">
                    <img src="{{ asset('images/team/placeholder.jpg') }}" alt="Lead Engineer" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-300 mb-1 horror-font">Jasper Crowe</h3>
                    <p class="text-gray-500 text-sm">Lead Engineer</p>
                </div>
                </div>
            <p class="mt-4 text-gray-700">Our dedicated team of designers, storytellers, and engineers work tirelessly to bring your darkest dreams to life.</p>
        </section>

        <section class="mb-12">
            <h2 class="text-3xl font-bold mb-6 horror-font">The Fear We Cultivate</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                At Horror-Bark, we believe that fear is a powerful emotion, capable of evoking both terror and exhilaration. We strive to craft experiences that tap into primal instincts, offering a unique blend of suspense, surprise, and sheer fright.
            </p>
            <p class="text-gray-700 leading-relaxed">
                Our commitment to safety is paramount. While we aim to thrill, we ensure that every attraction and experience is designed and maintained with the highest safety standards. Your well-being is as important as your fear.
            </p>
        </section>

        <section>
            <h2 class="text-3xl font-bold mb-6 horror-font">Connect with the Unseen</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Have questions or need to get in touch with the shadows? Reach out to our team.
            </p>
            <ul class="list-disc list-inside text-gray-700">
                <li>Email: <a href="mailto:info@horror-bark.com.mv" class="text-red-600 hover:underline">info@horror-bark.com</a></li>
                <li>Phone: +960 999999</li>
                <li>Location: 4.2260552262693,73.426287174225 </li>
            </ul>
        </section>
    </main>
@endsection {{-- End the content section --}}