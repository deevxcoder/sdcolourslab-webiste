import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:lab_desktop_app/services/api_service.dart';
import 'package:shimmer/shimmer.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  Map<String, dynamic>? _stats;
  bool _isLoading = true;
  String? _error;

  final ApiService _apiService = ApiService();
  final NumberFormat _currencyFormat = NumberFormat.currency(locale: 'en_IN', symbol: '₹');

  @override
  void initState() {
    super.initState();
    _fetchStats();
  }

  Future<void> _fetchStats() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    final result = await _apiService.get('/admin/dashboard');

    if (result['success'] == true) {
      setState(() {
        _stats = result['data'];
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
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Dashboard Overview',
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white),
                  ),
                  Text(
                    'Real-time metrics for SD Colours Lab.',
                    style: TextStyle(color: Colors.grey),
                  ),
                ],
              ),
              IconButton.filledTonal(
                onPressed: _fetchStats,
                icon: const Icon(Icons.refresh),
                tooltip: 'Refresh Data',
              ),
            ],
          ),
          const SizedBox(height: 32),
          if (_isLoading)
            _buildShimmerGrid()
          else if (_error != null)
            _buildErrorState()
          else
            _buildStatsGrid(),
          const SizedBox(height: 32),
          // Additional sections could go here (e.g., Recent Orders Table)
          const Text(
            'System Activity',
            style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white),
          ),
          const SizedBox(height: 16),
          Expanded(
            child: Container(
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.02),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.white.withOpacity(0.05)),
              ),
              child: const Center(
                child: Text('Activity logs and recent orders will appear here.', style: TextStyle(color: Colors.grey)),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatsGrid() {
    final data = _stats!;
    return GridView.count(
      crossAxisCount: 4,
      crossAxisSpacing: 24,
      mainAxisSpacing: 24,
      shrinkWrap: true,
      childAspectRatio: 1.6,
      children: [
        _buildStatCard('Total Revenue', _currencyFormat.format(data['total_revenue'] ?? 0), Icons.payments_outlined, Colors.greenAccent),
        _buildStatCard('Total Orders', data['total_orders'].toString(), Icons.shopping_bag_outlined, const Color(0xFFC9A227)),
        _buildStatCard('Active Photographers', data['active_photographers'].toString(), Icons.camera_alt_outlined, Colors.blueAccent),
        _buildStatCard('Pending Approvals', data['pending_photographers'].toString(), Icons.person_add_outlined, Colors.orangeAccent),
      ],
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: const Color(0xFF1A1D27),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(title, style: const TextStyle(color: Colors.grey, fontWeight: FontWeight.w600, fontSize: 13)),
              Icon(icon, color: color.withOpacity(0.6), size: 18),
            ],
          ),
          Text(
            value,
            style: const TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white),
          ),
          Container(
            height: 4,
            width: 40,
            decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(2)),
          ),
        ],
      ),
    );
  }

  Widget _buildShimmerGrid() {
    return Shimmer.fromColors(
      baseColor: const Color(0xFF1A1D27),
      highlightColor: const Color(0xFF2D3748),
      child: GridView.count(
        crossAxisCount: 4,
        crossAxisSpacing: 24,
        mainAxisSpacing: 24,
        shrinkWrap: true,
        childAspectRatio: 1.6,
        children: List.generate(4, (index) => Container(
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16)),
        )),
      ),
    );
  }

  Widget _buildErrorState() {
    return Center(
      child: Column(
        children: [
          const Icon(Icons.error_outline, color: Colors.redAccent, size: 48),
          const SizedBox(height: 16),
          Text(_error!, style: const TextStyle(color: Colors.redAccent)),
          const SizedBox(height: 16),
          ElevatedButton(onPressed: _fetchStats, child: const Text('Retry')),
        ],
      ),
    );
  }
}
