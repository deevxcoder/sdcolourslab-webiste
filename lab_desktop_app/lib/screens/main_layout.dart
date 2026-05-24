import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:lab_desktop_app/providers/auth_provider.dart';
import 'package:lab_desktop_app/screens/dashboard_screen.dart';
import 'package:lab_desktop_app/screens/orders_screen.dart';
import 'package:lab_desktop_app/screens/photographers_screen.dart';
import 'package:lab_desktop_app/screens/products_screen.dart';
import 'package:lab_desktop_app/screens/reports_screen.dart';
import 'package:lab_desktop_app/screens/broadcast_screen.dart';
import 'package:lab_desktop_app/screens/settings_screen.dart';

class MainLayout extends StatefulWidget {
  const MainLayout({super.key});

  @override
  State<MainLayout> createState() => _MainLayoutState();
}

class _MainLayoutState extends State<MainLayout> {
  int _selectedIndex = 0;

  final List<Widget> _screens = [
    const DashboardScreen(),
    const OrdersScreen(),
    const PhotographersScreen(),
    const ProductsScreen(),
    const ReportsScreen(),
    const BroadcastScreen(),
    const SettingsScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();

    return Scaffold(
      body: Row(
        children: [
          // Sidebar
          Container(
            width: 260,
            color: const Color(0xFF1A1D27),
            child: Column(
              children: [
                const SizedBox(height: 40),
                // Logo
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.palette_rounded, color: Color(0xFFC9A227), size: 32),
                    const SizedBox(width: 12),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'SD COLOURS',
                          style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.white, letterSpacing: 1),
                        ),
                        Text(
                          'LAB ADMIN',
                          style: TextStyle(fontSize: 10, color: Colors.white.withOpacity(0.5), letterSpacing: 2),
                        ),
                      ],
                    ),
                  ],
                ),
                const SizedBox(height: 40),
                // Nav Items
                _buildNavItem(0, Icons.dashboard_outlined, 'Dashboard'),
                _buildNavItem(1, Icons.shopping_bag_outlined, 'Orders'),
                _buildNavItem(2, Icons.camera_alt_outlined, 'Photographers'),
                _buildNavItem(3, Icons.inventory_2_outlined, 'Products'),
                _buildNavItem(4, Icons.bar_chart_rounded, 'Reports'),
                _buildNavItem(5, Icons.campaign_outlined, 'Broadcast'),
                _buildNavItem(6, Icons.settings_outlined, 'Settings'),
                const Spacer(),
                // User Profile & Logout
                Container(
                  padding: const EdgeInsets.all(16),
                  margin: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.05),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Column(
                    children: [
                      Row(
                        children: [
                          CircleAvatar(
                            radius: 16,
                            backgroundColor: const Color(0xFFC9A227),
                            child: Text(auth.user?.name[0].toUpperCase() ?? 'A', style: const TextStyle(color: Colors.black, fontWeight: FontWeight.bold, fontSize: 12)),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(auth.user?.name ?? 'Admin', style: const TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold), overflow: TextOverflow.ellipsis),
                                Text(auth.user?.email ?? 'admin@sdcolours.com', style: const TextStyle(color: Colors.grey, fontSize: 10), overflow: TextOverflow.ellipsis),
                              ],
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      SizedBox(
                        width: double.infinity,
                        child: TextButton.icon(
                          onPressed: () => auth.logout(),
                          icon: const Icon(Icons.logout, size: 16, color: Colors.redAccent),
                          label: const Text('Logout', style: TextStyle(color: Colors.redAccent, fontSize: 12)),
                          style: TextButton.styleFrom(
                            alignment: Alignment.centerLeft,
                            padding: const EdgeInsets.symmetric(horizontal: 0),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          // Content Area
          Expanded(
            child: Container(
              color: const Color(0xFF0F1117),
              child: _screens[_selectedIndex],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildNavItem(int index, IconData icon, String label) {
    bool isSelected = _selectedIndex == index;
    return InkWell(
      onTap: () => setState(() => _selectedIndex = index),
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFFC9A227).withOpacity(0.1) : Colors.transparent,
          borderRadius: BorderRadius.circular(8),
        ),
        child: Row(
          children: [
            Icon(icon, color: isSelected ? const Color(0xFFC9A227) : Colors.grey, size: 20),
            const SizedBox(width: 16),
            Text(
              label,
              style: TextStyle(
                color: isSelected ? Colors.white : Colors.grey,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                fontSize: 14,
              ),
            ),
            if (isSelected) const Spacer(),
            if (isSelected) Container(width: 4, height: 4, decoration: const BoxDecoration(color: Color(0xFFC9A227), shape: BoxShape.circle)),
          ],
        ),
      ),
    );
  }
}
