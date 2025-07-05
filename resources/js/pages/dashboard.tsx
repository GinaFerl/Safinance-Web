import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { LineChart, Line, XAxis, YAxis, Tooltip, CartesianGrid, ResponsiveContainer } from 'recharts';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

export default function Dashboard() {
  const { props } = usePage<{
    chartData: { month: string; income: number; expense: number; balance: number }[];
    recentReports: { month: string; income: number; expense: number; balance: number }[];
    }>();

  const chartData = props.chartData ?? [];
  const recentReports = props.recentReports ?? [];

  console.log("ChartData:", chartData);
  console.log("RecentReports:", recentReports);

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
        {/* Grafik Saldo Kas */}
        <Card>
          <CardHeader>
            <CardTitle>Grafik Saldo Kas</CardTitle>
          </CardHeader>
          <CardContent className="h-80">
            {chartData.length === 0 ? (
              <p className="text-muted-foreground">Belum ada data grafik.</p>
            ) : (
              <ResponsiveContainer width="100%" height="100%">
                <LineChart 
                    data={chartData}
                    margin={{ top: 20, right: 20, left: 35, bottom: 20 }}
                >
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="month" />
                    <YAxis />
                    <Tooltip formatter={(value: number) => `Rp ${value.toLocaleString('id-ID')}`} />
                    <Line type="monotone" dataKey="balance" stroke="#4f46e5" strokeWidth={2} />
                </LineChart>
              </ResponsiveContainer>
            )}
          </CardContent>
        </Card>

        {/* Laporan Bulanan Terakhir */}
        <Card>
          <CardHeader>
            <CardTitle>Laporan Bulanan Terakhir</CardTitle>
          </CardHeader>
          <CardContent>
            {recentReports.length === 0 ? (
              <p className="text-muted-foreground">Belum ada laporan tersedia.</p>
            ) : (
              <ul className="divide-y divide-border">
                {recentReports.map((r) => (
                <li key={r.month} className="py-3">
                    <div className="font-medium">{r.month}</div>
                    <div className="text-sm text-muted-foreground">
                    Income: Rp {r.income.toLocaleString('id-ID')}<br />
                    Expense: Rp {r.expense.toLocaleString('id-ID')}<br />
                    Balance: <strong>Rp {r.balance.toLocaleString('id-ID')}</strong>
                    </div>
                </li>
                ))}
              </ul>
            )}
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
}
