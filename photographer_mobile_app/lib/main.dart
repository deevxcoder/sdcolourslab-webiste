import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:photographer_mobile_app/providers/auth_provider.dart';
import 'package:photographer_mobile_app/providers/catalog_provider.dart';
import 'package:photographer_mobile_app/providers/order_provider.dart';
import 'package:photographer_mobile_app/providers/cart_provider.dart';
import 'package:photographer_mobile_app/screens/login_screen.dart';
import 'package:photographer_mobile_app/screens/register_screen.dart';
import 'package:photographer_mobile_app/screens/main_layout.dart';
import 'package:photographer_mobile_app/screens/pending_screen.dart';

void main() {
  runApp(const PhotographerApp());
}

class PhotographerApp extends StatelessWidget {
  const PhotographerApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()..checkSession()),
        ChangeNotifierProvider(create: (_) => CatalogProvider()),
        ChangeNotifierProvider(create: (_) => OrderProvider()),
        ChangeNotifierProvider(create: (_) => CartProvider()),
      ],
      child: MaterialApp(
        title: 'SD Colours Lab',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          brightness: Brightness.dark,
          primaryColor: const Color(0xFFC9A227),
          scaffoldBackgroundColor: const Color(0xFF0F111A),
          textTheme: GoogleFonts.interTextTheme(ThemeData.dark().textTheme),
          colorScheme: const ColorScheme.dark(
            primary: Color(0xFFC9A227),
            secondary: Color(0xFFC9A227),
            surface: Color(0xFF161922),
          ),
        ),
        home: const AuthWrapper(),
      ),
    );
  }
}

class AuthWrapper extends StatelessWidget {
  const AuthWrapper({super.key});

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();

    if (auth.isLoading) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator(color: Color(0xFFC9A227))),
      );
    }

    switch (auth.status) {
      case AuthStatus.authenticated:
        return const PhotographerMainLayout();
      case AuthStatus.pending:
        return const PendingScreen();
      case AuthStatus.rejected:
        return const Scaffold(
          body: Center(child: Text('Your account has been rejected.', style: TextStyle(color: Colors.red))),
        );
      case AuthStatus.unauthenticated:
      default:
        return const LoginScreen();
    }
  }
}
