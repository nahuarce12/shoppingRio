@extends('layouts.dashboard')

@php
{{-- Vista retirada: el flujo de solicitudes se gestiona íntegramente dentro del dashboard del dueño. --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allUsages as $usage)
                                        <tr>
                                            <td>{{ $usage->client->name }}</td>
                                            <td>{{ Str::limit($usage->promotion->description, 50) }}</td>
                                            <td>{{ $usage->usage_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if($usage->status === 'aceptada')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Aceptada
                                                    </span>
                                                @elseif($usage->status === 'rechazada')
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle"></i> Rechazada
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-clock"></i> Enviada
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        {{ $allUsages->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
