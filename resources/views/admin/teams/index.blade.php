@extends('layouts.app')

@section('title', 'Teams Management')

@section('content')
<div class="container-fluid" style="padding: 32px;">
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
        <div>
            <h1 style="margin: 0; font-size: 32px; font-weight: 800; color: #1a202c; letter-spacing: -0.5px;">Teams</h1>
            <p style="margin: 12px 0 0 0; color: #718096; font-size: 15px; font-weight: 500;">Manage enforcer teams and assignments</p>
        </div>
        <a href="{{ route('teams.create') }}" style="display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%); color: white; text-decoration: none; border-radius: 10px; font-weight: 700; transition: all 0.3s; box-shadow: 0 4px 15px rgba(43, 88, 255, 0.3); font-size: 15px;">
            <i class="fa-solid fa-plus" style="font-size: 18px;"></i>
            Create Team
        </a>
    </div>

    <!-- Teams Grid/Table -->
    @if($teams->count() > 0)
        <div style="background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%); border-bottom: 2px solid #1e42cc;">
                        <th style="padding: 20px 24px; text-align: left; font-weight: 700; color: #ffffff; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Team Name</th>
                        <th style="padding: 20px 24px; text-align: left; font-weight: 700; color: #ffffff; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Description</th>
                        <th style="padding: 20px 24px; text-align: center; font-weight: 700; color: #ffffff; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Members</th>
                        <th style="padding: 20px 24px; text-align: left; font-weight: 700; color: #ffffff; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Created By</th>
                        <th style="padding: 20px 24px; text-align: left; font-weight: 700; color: #ffffff; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Created Date</th>
                        <th style="padding: 20px 24px; text-align: center; font-weight: 700; color: #ffffff; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teams as $team)
                        <tr style="border-bottom: 1px solid #e2e8f0; transition: all 0.3s ease; background: #ffffff;">
                            <td style="padding: 18px 24px; color: #1a202c; font-weight: 700; font-size: 15px;">
                                <a href="{{ route('teams.show', $team) }}" style="color: #2b58ff; text-decoration: none; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-people-group" style="font-size: 14px;"></i>
                                    {{ $team->name }}
                                </a>
                            </td>
                            <td style="padding: 18px 24px; color: #4a5568; font-size: 14px; line-height: 1.5;">
                                {{ Str::limit($team->description ?? 'No description', 50) }}
                            </td>
                            <td style="padding: 18px 24px; text-align: center;">
                                <span style="display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); color: #1e42cc; border-radius: 24px; font-size: 14px; font-weight: 700; min-width: 50px;">
                                    {{ $team->members_count ?? $team->members()->count() }}
                                </span>
                            </td>
                            <td style="padding: 18px 24px; color: #4a5568; font-size: 14px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 32px; height: 32px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #2b58ff; font-weight: 700; font-size: 12px;">
                                        {{ strtoupper(substr($team->creator->f_name ?? 'N', 0, 1)) }}
                                    </div>
                                    {{ $team->creator->f_name ?? 'N/A' }} {{ $team->creator->l_name ?? '' }}
                                </div>
                            </td>
                            <td style="padding: 18px 24px; color: #4a5568; font-size: 14px;">
                                <span style="display: inline-block; padding: 6px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748; font-weight: 500;">
                                    {{ $team->created_at->format('M d, Y') }}
                                </span>
                            </td>
                            <td style="padding: 18px 24px; text-align: center;">
                                <div style="display: flex; gap: 10px; justify-content: center;">
                                    <a href="{{ route('teams.show', $team) }}" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #e3f2fd; color: #2b58ff; border-radius: 8px; text-decoration: none; transition: all 0.3s; font-size: 16px; border: 1px solid #bbdefb;" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('teams.edit', $team) }}" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #f0f0f0; color: #666; border-radius: 8px; text-decoration: none; transition: all 0.3s; font-size: 16px; border: 1px solid #e2e8f0;" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('teams.destroy', $team) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this team?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #fee; color: #dc3545; border-radius: 8px; border: 1px solid #fdd; cursor: pointer; transition: all 0.3s; font-size: 16px;" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($teams->hasPages())
            <div style="margin-top: 32px; display: flex; justify-content: center;">
                {{ $teams->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div style="background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); border-radius: 16px; padding: 80px 32px; text-align: center; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05); border: 2px dashed #cbd5e0;">
            <div style="display: inline-block; margin-bottom: 24px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i style="font-size: 40px; color: #2b58ff;" class="fa-solid fa-people-group"></i>
                </div>
            </div>
            <h3 style="margin: 0 0 12px 0; color: #1a202c; font-size: 24px; font-weight: 700;">No Teams Yet</h3>
            <p style="color: #718096; font-size: 15px; margin: 0 0 24px 0; max-width: 400px; margin-left: auto; margin-right: auto;">Start by creating your first team to organize enforcers and manage assignments effectively.</p>
            <a href="{{ route('teams.create') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s;">
                <i class="fa-solid fa-plus"></i>
                Create First Team
            </a>
        </div>
    @endif
</div>

<style>
    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
    }

    table tbody tr:hover {
        background: #f7fafc !important;
        box-shadow: inset 0 0 8px rgba(43, 88, 255, 0.05);
    }

    table tbody tr:hover a {
        color: #1e42cc;
    }

    table tbody tr:hover button,
    table tbody tr:hover .fa-solid {
        transform: scale(1.1);
    }

    a[href*="teams"], button[type="submit"] {
        cursor: pointer;
    }

    a[href*="teams"]:hover {
        opacity: 0.9;
    }

    button[type="submit"]:hover {
        background-color: #fcc !important;
    }

    .pagination {
        gap: 8px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
    }

    .pagination a, .pagination span {
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        color: #2b58ff;
        text-decoration: none;
        transition: all 0.3s;
        font-weight: 500;
    }

    .pagination a:hover {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-color: #2b58ff;
        transform: translateY(-2px);
    }

    .pagination .active span {
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: white;
        border-color: #2b58ff;
        box-shadow: 0 4px 12px rgba(43, 88, 255, 0.3);
    }

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 20px !important;
        }

        table {
            font-size: 12px !important;
        }

        table thead th,
        table tbody td {
            padding: 14px 12px !important;
        }

        a[style*="inline-flex"],
        button[type="submit"] {
            width: 36px !important;
            height: 36px !important;
        }
    }

    @media (max-width: 480px) {
        table thead th,
        table tbody td {
            padding: 12px 8px !important;
            font-size: 11px !important;
        }

        a[style*="inline-flex"],
        button[type="submit"] {
            width: 32px !important;
            height: 32px !important;
        }

        a[style*="inline-flex"] i,
        button[type="submit"] i {
            font-size: 14px !important;
        }
    }
</style>
@endsection
