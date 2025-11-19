@extends('layouts.app')

@section('title', 'Create Team')

@section('content')
<div class="container-fluid" style="padding: 32px; max-width: 900px; margin: 0 auto;">
    <!-- Header Section -->
    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 32px;">
        <a href="{{ route('teams.index') }}" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #f0f0f0; color: #666; border-radius: 8px; text-decoration: none; transition: all 0.3s; font-size: 18px;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #333;">Create New Team</h1>
            <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">Create a team and assign enforcers</p>
        </div>
    </div>

    <!-- Form Card -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 32px;">
        <form action="{{ route('teams.store') }}" method="POST">
            @csrf

            <!-- Team Name Field -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 14px;">
                    Team Name <span style="color: #dc3545;">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter team name" style="width: 100%; padding: 12px 16px; border: 1px solid {{ $errors->has('name') ? '#dc3545' : '#ddd' }}; border-radius: 8px; font-size: 14px; transition: all 0.3s; box-sizing: border-box;" onfocus="this.style.borderColor='#2b58ff'; this.style.boxShadow='0 0 0 3px rgba(43, 88, 255, 0.1)'" onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none'">
                @error('name')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Description Field -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 14px;">
                    Description
                </label>
                <textarea name="description" placeholder="Enter team description" rows="3" style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; transition: all 0.3s; box-sizing: border-box; font-family: inherit; resize: vertical;" onfocus="this.style.borderColor='#2b58ff'; this.style.boxShadow='0 0 0 3px rgba(43, 88, 255, 0.1)'" onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none'">{{ old('description') }}</textarea>
                @error('description')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Notes Field -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 14px;">
                    Notes
                </label>
                <textarea name="notes" placeholder="Enter any additional notes" rows="3" style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; transition: all 0.3s; box-sizing: border-box; font-family: inherit; resize: vertical;" onfocus="this.style.borderColor='#2b58ff'; this.style.boxShadow='0 0 0 3px rgba(43, 88, 255, 0.1)'" onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none'">{{ old('notes') }}</textarea>
                @error('notes')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Enforcers Selection -->
            <div style="margin-bottom: 32px;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 12px; font-size: 14px;">
                    Assign Enforcers
                </label>
                <div style="background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 12px; max-height: 300px; overflow-y: auto;">
                    @if($enforcers->count() > 0)
                        <div id="enforcersContainer">
                            @foreach($enforcers as $enforcer)
                                <label style="display: flex; align-items: center; gap: 10px; padding: 12px; background: white; border: 1px solid #e9ecef; border-radius: 6px; margin-bottom: 8px; cursor: pointer; transition: all 0.3s;">
                                    <input type="checkbox" name="enforcers[]" value="{{ $enforcer->id }}" {{ in_array($enforcer->id, old('enforcers', [])) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #333; font-size: 14px;">
                                            {{ $enforcer->f_name }} {{ $enforcer->l_name }}
                                        </div>
                                        <div style="font-size: 12px; color: #666;">
                                            {{ $enforcer->email }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div style="padding: 32px 16px; text-align: center; color: #999;">
                            <i style="font-size: 32px; display: block; margin-bottom: 12px; color: #ddd;" class="fa-solid fa-people-group"></i>
                            <p style="margin: 0;">No active enforcers available. Create enforcers first.</p>
                        </div>
                    @endif
                </div>
                @error('enforcers')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid #e9ecef;">
                <button type="submit" style="flex: 1; padding: 12px 24px; background: #2b58ff; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 14px; box-shadow: 0 2px 8px rgba(43, 88, 255, 0.2);">
                    <i class="fa-solid fa-check" style="margin-right: 8px;"></i>
                    Create Team
                </button>
                <a href="{{ route('teams.index') }}" style="flex: 1; padding: 12px 24px; background: #f0f0f0; color: #666; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 14px; text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-xmark"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    input[type="checkbox"] {
        cursor: pointer;
        accent-color: #2b58ff;
    }

    label {
        user-select: none;
    }

    label:hover {
        background: #f5f5f5 !important;
    }

    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(43, 88, 255, 0.3) !important;
    }

    a[href*="teams"]#cancel-link:hover {
        background: #e9ecef !important;
    }
</style>
@endsection
