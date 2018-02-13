@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-5">
            <h4>Available Attributes</h4>
            <button type="button" class="btn btn-success">
                <i class="fas fa-fw fa-plus"></i> Add Attribute
            </button>
        </div>
        <div class="col-md-2">
            <h4>Available Context-Types</h4>
            <button type="button" class="btn btn-success">
                <i class="fas fa-fw fa-plus"></i> New Context-Type
            </button>
        </div>
        <div class="col-md-5">
            <h4>Added Attributes</h4>
            <button type="button" class="btn btn-success">
                <i class="fas fa-fw fa-plug"></i> Add Attribute to <code>Name</code>.
            </button>
            <p class="alert alert-info">
                List of attributes connected to the selected CT.
            </p>
        </div>
    </div>
@endsection