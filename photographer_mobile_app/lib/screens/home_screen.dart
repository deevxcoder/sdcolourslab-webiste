import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:photographer_mobile_app/providers/auth_provider.dart';
import 'package:photographer_mobile_app/services/api_service.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  Map<String, dynamic>? _dashboardData;
  bool _isLoading = true;
  final _currencyFormat = NumberFormat.currency(locale: 'en_IN', symbol: '₹');

  @override
  void initState() {
    super.initState();
    _fetchDashboard();
  }

  Future<void> _fetchDashboard() async {
    final api = ApiService();
    final result = await api.get('/photographer/dashboard');
    if (result['success'] == true && mounted) {
      setState(() {
        _dashboardData = result['data'];
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Scaffold(
      backgroundColor: const Color(0xFF0F111A),
      body: RefreshIndicator(
        onRefresh: _fetchDashboard,
        color: const Color(0xFFC9A227),
        child: CustomScrollView(
          slivers: [
            _buildAppBar(user?.studioName ?? 'Studio'),
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildStatsRow(),
                    const SizedBox(height: 24),
                    if (!_isLoading && _dashboardData?['announcement'] != null)
                      _buildAnnouncementBanner(),
                    const SizedBox(height: 32),
                    Text(
                      'Recent Orders',
                      style: GoogleFonts.outfit(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 16),
                    _buildRecentOrders(),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAppBar(String studioName) {
    return SliverAppBar(
      expandedHeight: 120,
      backgroundColor: const Color(0xFF0F111A),
      flexibleSpace: FlexibleSpaceBar(
        titlePadding: const EdgeInsets.only(left: 20, bottom: 16),
        title: Column(
          mainAxisAlignment: MainAxisAlignment.end,
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'Hello,',
              style: GoogleFonts.inter(fontSize: 12, color: Colors.white.withOpacity(0.5)),
            ),
            Text(
              studioName,
              style: GoogleFonts.outfit(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white),
            ),
          ],
        ),
      ),
      actions: [
        IconButton(
          icon: const Icon(Icons.notifications_none_outlined, color: Colors.white),
          onPressed: () {},
        ),
        const SizedBox(width: 10),
      ],
    );
  }

  Widget _buildStatsRow() {
    if (_isLoading) return const Center(child: CircularProgressIndicator(color: Color(0xFFC9A227)));

    final totalOrders = _dashboardData?['total_orders'] ?? 0;
    final totalSpent = _dashboardData?['total_spent'] ?? 0.0;

    return Row(
      children: [
        Expanded(
          child: _statCard('TOTAL ORDERS', totalOrders.toString(), Icons.shopping_bag_outlined),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: _statCard('TOTAL SPENT', _currencyFormat.format(totalSpent), Icons.payments_outlined),
        ),
      ],
    );
  }

  Widget _buildAnnouncementBanner() {
    final announcement = _dashboardData?['announcement'];
    final type = announcement['type'] ?? 'info';
    final color = type == 'offer' ? Colors.orangeAccent : (type == 'warning' ? Colors.redAccent : const Color(0xFFC9A227));
    
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [color.withOpacity(0.15), color.withOpacity(0.05)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                type == 'offer' ? Icons.local_offer : (type == 'warning' ? Icons.warning_amber : Icons.campaign_rounded),
                color: color,
                size: 20,
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  announcement['title'],
                  style: GoogleFonts.outfit(color: color, fontWeight: FontWeight.bold, fontSize: 16),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Text(
            announcement['message'],
            style: GoogleFonts.inter(color: Colors.white.withOpacity(0.8), fontSize: 14, height: 1.5),
          ),
        ],
      ),
    );
  }

  Widget _statCard(String label, String value, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF161922),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: const Color(0xFFC9A227), size: 24),
          const SizedBox(height: 16),
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 10, fontWeight: FontWeight.bold)),
          const SizedBox(height: 4),
          Text(value, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildRecentOrders() {
    if (_isLoading) return const SizedBox();

    final orders = _dashboardData?['recent_orders'] as List? ?? [];
    if (orders.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.only(top: 40),
          child: Text('No orders yet.', style: TextStyle(color: Colors.white.withOpacity(0.3))),
        ),
      );
    }

    return Column(
      children: orders.map((o) => _orderTile(o)).toList(),
    );
  }

  Widget _orderTile(Map<String, dynamic> order) {
    final statusColor = _getStatusColor(order['status']);
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF161922),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(Icons.description_outlined, color: statusColor, size: 20),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Order #${order['id']}', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                Text('${order['item_count']} items', style: TextStyle(color: Colors.white.withOpacity(0.5), fontSize: 12)),
              ],
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(_currencyFormat.format(order['total']), style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              Text(
                order['status'].toString().toUpperCase(),
                style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.bold),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'pending': return Colors.orangeAccent;
      case 'processing': return Colors.blueAccent;
      case 'shipped': return Colors.purpleAccent;
      case 'completed': return Colors.greenAccent;
      default: return Colors.grey;
    }
  }
}
