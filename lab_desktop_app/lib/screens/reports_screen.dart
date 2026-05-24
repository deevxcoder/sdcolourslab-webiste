import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:intl/intl.dart';
import 'package:lab_desktop_app/services/api_service.dart';

class ReportsScreen extends StatefulWidget {
  const ReportsScreen({super.key});

  @override
  State<ReportsScreen> createState() => _ReportsScreenState();
}

class _ReportsScreenState extends State<ReportsScreen> {
  final ApiService _apiService = ApiService();
  bool _isLoading = true;
  String? _error;
  Map<String, dynamic>? _reportData;

  @override
  void initState() {
    super.initState();
    _fetchReport();
  }

  Future<void> _fetchReport() async {
    setState(() => _isLoading = true);
    final result = await _apiService.get('/admin/reports');
    if (result['success'] == true) {
      setState(() {
        _reportData = result['data'];
        _isLoading = false;
      });
    } else {
      setState(() {
        _error = result['message'];
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(32.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Analytics & Visualizations',
            style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white),
          ),
          const Text(
            'Deep-dive into laboratory performance and trends.',
            style: TextStyle(color: Colors.grey),
          ),
          const SizedBox(height: 32),
          if (_isLoading)
            const Expanded(child: Center(child: CircularProgressIndicator(color: Color(0xFFC9A227))))
          else if (_error != null)
            Center(child: Text(_error!, style: const TextStyle(color: Colors.redAccent)))
          else
            Expanded(
              child: SingleChildScrollView(
                child: Column(
                  children: [
                    _buildRevenueChart(),
                    const SizedBox(height: 32),
                    Row(
                      children: [
                        Expanded(child: _buildCategoryDistribution()),
                        const SizedBox(width: 32),
                        Expanded(child: _buildSummaryGrid()),
                      ],
                    ),
                  ],
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildRevenueChart() {
    final List<dynamic> data = _reportData?['revenue_by_month'] ?? [];
    if (data.isEmpty) return const SizedBox();

    // Reverse for chronological order
    final sortedData = data.reversed.toList();

    return Container(
      height: 350,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: const Color(0xFF1A1D27),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Revenue Trend (Monthly)', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 32),
          Expanded(
            child: LineChart(
              LineChartData(
                gridData: FlGridData(show: true, drawVerticalLine: false, horizontalInterval: 10000),
                titlesData: FlTitlesData(
                  bottomTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      getTitlesWidget: (value, meta) {
                        int index = value.toInt();
                        if (index >= 0 && index < sortedData.length) {
                          return Padding(
                            padding: const EdgeInsets.only(top: 8.0),
                            child: Text(sortedData[index]['month'], style: const TextStyle(color: Colors.grey, fontSize: 10)),
                          );
                        }
                        return const SizedBox();
                      },
                    ),
                  ),
                  leftTitles: AxisTitles(sideTitles: SideTitles(showTitles: true, reservedSize: 60, getTitlesWidget: (val, meta) => Text('₹${NumberFormat.compact().format(val)}', style: const TextStyle(color: Colors.grey, fontSize: 10)))),
                  topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                  rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                ),
                borderData: FlBorderData(show: false),
                lineBarsData: [
                  LineChartBarData(
                    spots: sortedData.asMap().entries.map((e) => FlSpot(e.key.toDouble(), double.parse(e.value['revenue'].toString()))).toList(),
                    isCurved: true,
                    color: const Color(0xFFC9A227),
                    barWidth: 4,
                    dotData: const FlDotData(show: true),
                    belowBarData: BarAreaData(show: true, color: const Color(0xFFC9A227).withOpacity(0.1)),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCategoryDistribution() {
    final List<dynamic> data = _reportData?['category_volume'] ?? [];
    final colors = [const Color(0xFFC9A227), Colors.blueAccent, Colors.purpleAccent, Colors.greenAccent, Colors.orangeAccent];

    return Container(
      height: 300,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: const Color(0xFF1A1D27),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Order Volume by Category', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          Expanded(
            child: PieChart(
              PieChartData(
                sections: data.asMap().entries.map((e) {
                  return PieChartSectionData(
                    color: colors[e.key % colors.length],
                    value: double.parse(e.value['count'].toString()),
                    title: '${e.value['category']}\n${e.value['count']}',
                    radius: 70,
                    titleStyle: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.white),
                  );
                }).toList(),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryGrid() {
    final List<dynamic> data = _reportData?['revenue_by_month'] ?? [];
    double totalRev = 0;
    int totalCount = 0;
    for (var m in data) {
      totalRev += double.parse(m['revenue'].toString());
      totalCount += int.parse(m['count'].toString());
    }

    return Container(
      height: 300,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: const Color(0xFF1A1D27),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('KPI Summary', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
          const Spacer(),
          _kpiRow('Lifetime Revenue', '₹${NumberFormat.currency(symbol: '', decimalDigits: 0).format(totalRev)}'),
          const Divider(color: Colors.white10),
          _kpiRow('Total Orders Processed', totalCount.toString()),
          const Divider(color: Colors.white10),
          _kpiRow('Avg. Order Value', '₹${totalCount > 0 ? (totalRev / totalCount).toStringAsFixed(2) : '0'}'),
          const Spacer(),
        ],
      ),
    );
  }

  Widget _kpiRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey)),
          Text(value, style: const TextStyle(color: Color(0xFFC9A227), fontWeight: FontWeight.bold, fontSize: 18)),
        ],
      ),
    );
  }
}
