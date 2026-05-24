import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:lab_desktop_app/providers/auth_provider.dart';
import 'package:lab_desktop_app/providers/order_provider.dart';
import 'package:lab_desktop_app/providers/photographer_provider.dart';
import 'package:lab_desktop_app/providers/catalog_provider.dart';
import 'package:lab_desktop_app/screens/login_screen.dart';
import 'package:lab_desktop_app/screens/main_layout.dart';

void main() {
  runApp(const SDColoursLabApp());
}

class SDColoursLabApp extends StatelessWidget {
  const SDColoursLabApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()..checkSession()),
        ChangeNotifierProvider(create: (_) => OrderProvider()),
        ChangeNotifierProvider(create: (_) => PhotographerProvider()),
        ChangeNotifierProvider(create: (_) => CatalogProvider()),
      ],
      child: MaterialApp(
        title: 'SD Colours Lab Admin',
        debugShowCheckedModeBanner: false,
        theme: _buildTheme(Brightness.dark),
        home: const AuthWrapper(),
      ),
    );
  }

  ThemeData _buildTheme(Brightness brightness) {
    var baseTheme = ThemeData(
      brightness: brightness,
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: const Color(0xFFC9A227), // Gold/Amber accent
        brightness: brightness,
        surface: const Color(0xFF0F1117), // Deep Navy background
      ),
      scaffoldBackgroundColor: const Color(0xFF0F1117),
    );

    return baseTheme.copyWith(
      textTheme: GoogleFonts.interTextTheme(baseTheme.textTheme),
    );
  }
}

class AuthWrapper extends StatelessWidget {
  const AuthWrapper({super.key});

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    
    if (auth.isAuthenticated) {
      return const MainLayout();
    }
    
    return const LoginScreen();
  }
}
