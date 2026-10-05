@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Student Interview Recruitment</h1>
        <p class="text-xl text-gray-600">Choose your track to start the registration process</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Technical Track -->
        <a href="{{ route('registration.track', 'technical') }}" 
           class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 hover:shadow-lg hover:border-primary-300 transition-all duration-300 group">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-100 text-primary-600 group-hover:bg-primary-600 group-hover:text-white transition-colors">
                    <i class="fas fa-code text-3xl"></i>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-3">Technical</h2>
            <p class="text-gray-600 text-center mb-6">For Computer Science, Engineering, Data Science, Cybersecurity, DevOps and related fields</p>
            
            <div class="space-y-2 text-sm text-gray-500 border-t pt-4">
                @php
                    $technical = \App\Models\Track::where('name', 'Technical')->first();
                    if ($technical) {
                        $subTracks = $technical->children()->active()->orderBy('name')->get();
                        foreach ($subTracks as $sub) {
                            echo '<div class="flex items-center justify-center"><i class="fas fa-chevron-right mr-2 text-primary-400"></i>' . $sub->name . '</div>';
                        }
                    }
                @endphp
            </div>
            
            <div class="mt-6 pt-4 border-t">
                <span class="inline-flex w-full items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors group-hover:bg-primary-700">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Apply for Technical
                </span>
            </div>
        </a>

        <!-- Non-Technical Track -->
        <a href="{{ route('registration.track', 'non-technical') }}" 
           class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 hover:shadow-lg hover:border-primary-300 transition-all duration-300 group">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-secondary-100 text-secondary-600 group-hover:bg-secondary-600 group-hover:text-white transition-colors">
                    <i class="fas fa-briefcase text-3xl"></i>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-3">Non-Technical</h2>
            <p class="text-gray-600 text-center mb-6">For Business, Marketing, HR, Design, Management and creative fields</p>
            
            <div class="space-y-2 text-sm text-gray-500 border-t pt-4">
                @php
                    $nonTechnical = \App\Models\Track::where('name', 'Non-Technical')->first();
                    if ($nonTechnical) {
                        $subTracks = $nonTechnical->children()->active()->orderBy('name')->get();
                        foreach ($subTracks as $sub) {
                            echo '<div class="flex items-center justify-center"><i class="fas fa-chevron-right mr-2 text-secondary-400"></i>' . $sub->name . '</div>';
                        }
                    }
                @endphp
            </div>
            
            <div class="mt-6 pt-4 border-t">
                <span class="inline-flex w-full items-center px-4 py-2 bg-secondary-600 text-white font-medium rounded-lg hover:bg-secondary-700 transition-colors group-hover:bg-secondary-700">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Apply for Non-Technical
                </span>
            </div>
        </a>
    </div>
</div>
@endsection