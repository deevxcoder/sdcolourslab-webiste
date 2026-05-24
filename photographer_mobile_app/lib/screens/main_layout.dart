import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:photographer_mobile_app/providers/auth_provider.dart';
import 'package:photographer_mobile_app/screens/home_screen.dart';
import 'package:photographer_mobile_app/screens/catalog_screen.dart';
import 'package:photographer_mobile_app/screens/orders_screen.dart';

class PhotographerMainLayout extends StatefulWidget {
  const PhotographerMainLayout({super.key});

  @override
  State<PhotographerMainLayout> createState() => _PhotographerMainLayoutState();
}

class _PhotographerMainLayoutState extends State<PhotographerMainLayout> {
  int _selectedIndex = 0;

  final List<Widget> _screens = [
    const HomeScreen(),
    const CatalogScreen(),
    const OrdersScreen(),
    const Center(child: Text('Profile', style: TextStyle(color: Colors.white))),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0F111A),
      body: _screens[_selectedIndex],
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _selectedIndex,
        onTap: (index) {
          if (index == 3) {
            _showProfileSheet(context);
            return;
          }
          setState(() => _selectedIndex = index);
        },
        type: BottomNavigationBarType.fixed,
        backgroundColor: const Color(0xFF161922),
        selectedItemColor: const Color(0xFFC9A227),
        unselectedItemColor: Colors.white.withOpacity(0.4),
        selectedFontSize: 12,
        unselectedFontSize: 12,
        elevation: 10,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home_outlined), activeIcon: Icon(Icons.home), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.grid_view_outlined), activeIcon: Icon(Icons.grid_view), label: 'Catalog'),
          BottomNavigationBarItem(icon: Icon(Icons.shopping_bag_outlined), activeIcon: Icon(Icons.shopping_bag), label: 'Orders'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), activeIcon: Icon(Icons.person), label: 'Profile'),
        ],
      ),
    );
  }

  void _showProfileSheet(BuildContext context) {
    final user = context.read<AuthProvider>().user;
    showModalBottomSheet(
      context: context,
      backgroundColor: const Color(0xFF161922),
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (context) => Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Row(
              children: [
                CircleAvatar(
                  radius: 30,
                  backgroundColor: const Color(0xFFC9A227).withOpacity(0.1),
                  child: const Icon(Icons.person, color: Color(0xFFC9A227), size: 30),
                ),
                const SizedBox(width: 16),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(user?.name ?? 'User', style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                    Text(user?.email ?? '', style: const TextStyle(color: Colors.grey, fontSize: 13)),
                  ],
                ),
              ],
            ),
            const SizedBox(height: 24),
            const Divider(color: Colors.white10),
            _buildActionItem(Icons.settings_outlined, 'Settings', () {}),
            _buildActionItem(Icons.help_outline, 'Help & Support', () {}),
            _buildActionItem(Icons.logout, 'Log Out', () {
              Navigator.pop(context);
              context.read<AuthProvider>().logout();
            }, color: Colors.redAccent),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  Widget _buildActionItem(IconData icon, String label, VoidCallback onTap, {Color color = Colors.white70}) {
    return ListTile(
      leading: Icon(icon, color: color, size: 22),
      title: Text(label, style: TextStyle(color: color, fontSize: 15)),
      onTap: onTap,
    );
  }
}
