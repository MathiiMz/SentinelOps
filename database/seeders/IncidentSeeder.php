<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $analysts = User::where('role', 'analyst')->get();

        $incidents = [
            [
                'title' => 'Intento de acceso no autorizado en servidor web',
                'description' => 'Se detectaron múltiples intentos de login fallidos desde IP externa',
                'severity' => 'critical',
                'source_ip' => '192.168.1.100',
                'affected_host' => 'web-server-01.local',
            ],
            [
                'title' => 'Actividad sospechosa en base de datos',
                'description' => 'Queries anómalas detectadas en la BD de producción',
                'severity' => 'high',
                'source_ip' => '172.16.0.50',
                'affected_host' => 'db-server-01.local',
            ],
            [
                'title' => 'Malware detectado en estación de trabajo',
                'description' => 'Antivirus detectó proceso malicioso en workstation',
                'severity' => 'high',
                'source_ip' => '10.0.0.25',
                'affected_host' => 'workstation-05.local',
            ],
            [
                'title' => 'Cambio no autorizado en permisos de usuario',
                'description' => 'Se modificaron permisos de usuario administrativo',
                'severity' => 'medium',
                'source_ip' => '192.168.1.15',
                'affected_host' => 'domain-controller.local',
            ],
            [
                'title' => 'Posible ataque DDoS detectado',
                'description' => 'Tráfico inusual desde múltiples IPs externas',
                'severity' => 'critical',
                'source_ip' => '203.0.113.0',
                'affected_host' => 'firewall.local',
            ],
            [
                'title' => 'Certificado SSL vencido en servidor',
                'description' => 'Certificado SSL expirado detectado en servidor HTTPS',
                'severity' => 'low',
                'source_ip' => '192.168.1.80',
                'affected_host' => 'api-server.local',
            ],
        ];

        foreach ($incidents as $index => $incident) {
            $analyst = $analysts[$index % count($analysts)];
            
            Incident::create([
                ...$incident,
                'status' => $index % 2 === 0 ? 'investigating' : 'open',
                'created_by' => $admin->id,
                'assigned_to' => $analyst->id,
            ]);
        }
    }
}
