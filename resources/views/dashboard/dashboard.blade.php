@extends('../home')

@section('title', 'Dashboard - Hotel Project')

@section('content')
<div class="height-100 p-5">
<div class="d-flex justify-content-between align-items-center mb-4">
        <div class="card" style="flex: 1; margin: 10px;">
            <div class="card-body bg-white" style="background-color: #EEEEEE;">
                <div style="width: 100%;"><canvas id="myChart2"></canvas></div>
            </div>
        </div>
        <div class="card" style="flex: 1; margin: 10px;">
            <div class="card-body bg-white" style="background-color: #EEEEEE;">
                <div style="width: 100%;"><canvas id="myChart3"></canvas></div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="card" style="flex: 1; margin: 10px;">
            <div class="card-body bg-white" style="background-color: #EEEEEE;">
                <div style="width: 100%;"><canvas id="myChart"></canvas></div>
            </div>
        </div>    
        
        <div class="card" style="flex: 1; margin: 10px;">
            <div class="card-body bg-white" style="background-color: #EEEEEE;">
                <div style="width: 100%;"><canvas id="myChart4"></canvas></div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-body {
        font-size: 1.2em; /* Aumenta el tamaño de la letra */
    }
</style>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

@vite('resources/js/dashboard.js')
@endsection
