import React, { useState, useEffect } from "react";
import {
  Button,
  Row,
  Col,
  Input,
  message,
  Card,
  Tag,
  Divider,
  List,
  Typography,
  Avatar,
  Drawer,
  Grid,
} from "antd";
import Lottie from "lottie-react";
import {
  PhoneOutlined,
  MailOutlined,
  EnvironmentOutlined,
  CheckCircleOutlined,
  ArrowRightOutlined,
  MenuOutlined,
  CloseOutlined,
  SafetyCertificateOutlined,
  TeamOutlined,
  CrownOutlined,
  EyeOutlined,
  LockOutlined,
  CameraOutlined,
  SecurityScanOutlined,
  WhatsAppOutlined,
} from "@ant-design/icons";
import { FaShieldHalved } from "react-icons/fa6";
import securityImage from "/securityStanding.jpg";

const { TextArea } = Input;
const { Text, Title } = Typography;
const { useBreakpoint } = Grid;

const ShieldTracLanding = () => {
  const [scrollY, setScrollY] = useState(0);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [contactForm, setContactForm] = useState({
    name: "",
    email: "",
    phone: "",
    message: "",
  });
  const screens = useBreakpoint();
  const isMobile = !screens.md;

  useEffect(() => {
    const handleScroll = () => setScrollY(window.scrollY);
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  // Professional blue and orange color scheme
  const colors = {
    primary: "#1E40AF",
    secondary: "#EA580C",
    darkBlue: "#1E3A8A",
    lightBlue: "#E0F2FE",
    lightOrange: "#FFF7ED",
    dark: "#1F2937",
    gray: "#6B7280",
    lightGray: "#F8FAFC",
    text: {
      primary: "#1F2937",
      secondary: "#6B7280",
      light: "#9CA3AF",
      white: "#FFFFFF"
    }
  };

  // Services from the ShieldTrac document
  const securityServices = [
    {
      title: "Manned Security",
      icon: <TeamOutlined />,
      description:
        "Highly trained security guards providing visible presence and threat deterrence",
      features: ["24/7 Guard Services", "Access Control", "Patrol Services"],
    },
    {
      title: "Cash Management",
      icon: <LockOutlined />,
      description: "Secure cash handling and transportation services",
      features: ["Cash in Transit", "Secure Transport", "Armored Vehicles"],
    },
    {
      title: "Private Investigations",
      icon: <EyeOutlined />,
      description: "Discreet and professional investigative services",
      features: [
        "Background Checks",
        "Surveillance",
        "Corporate Investigations",
      ],
    },
    {
      title: "Alarm Systems",
      icon: <SecurityScanOutlined />,
      description: "Advanced alarm systems with emergency response",
      features: ["Fire Alarms", "Emergency Response", "24/7 Monitoring"],
    },
    {
      title: "CCTV Security",
      icon: <CameraOutlined />,
      description: "Complete CCTV installation and monitoring solutions",
      features: ["HD Surveillance", "Remote Monitoring", "Recording Systems"],
    },
    {
      title: "Access Solutions",
      icon: <FaShieldHalved />,
      description: "Electronic access control and perimeter security",
      features: ["Electric Gates", "Fencing Systems", "Access Control"],
    },
  ];

  const whyChooseUs = [
    {
      icon: <TeamOutlined />,
      title: "Expert Security Team",
      description:
        "Thoroughly checked, screened, and vetted security professionals",
    },
    {
      icon: <SafetyCertificateOutlined />,
      title: "Proven Experience",
      description:
        "Over a decade of security service with excellent reputation",
    },
    {
      icon: <CrownOutlined />,
      title: "Professional Standards",
      description: "Highest level of professionalism and ethical conduct",
    },
    {
      icon: <FaShieldHalved />,
      title: "Customized Solutions",
      description: "Tailored security plans for each client's unique needs",
    },
    {
      icon: <CheckCircleOutlined />,
      title: "Quality Assurance",
      description: "Unmatchable quality at competitive prices",
    },
    {
      icon: <PhoneOutlined />,
      title: "24/7 Support",
      description: "Round-the-clock customer support and emergency response",
    },
  ];

  const testimonials = [
    {
      name: "Retail Client",
      company: "Major Retail Chain",
      role: "Security Manager",
      content:
        "ShieldTrac's manned security services have significantly improved our store safety and reduced incidents by over 60%.",
    },
    {
      name: "Corporate Client",
      company: "Financial Institution",
      role: "Operations Director",
      content:
        "Their cash management services are reliable and professional. We've trusted them with our cash operations for 5 years.",
    },
    {
      name: "Industrial Client",
      company: "Manufacturing Plant",
      role: "Facility Manager",
      content:
        "The CCTV and access control systems installed by ShieldTrac have transformed our site security.",
    },
    {
      name: "Commercial Client",
      company: "Office Complex",
      role: "Property Manager",
      content:
        "Professional, reliable, and always responsive. ShieldTrac delivers exceptional security services.",
    },
  ];

  const stats = [
    { number: "10+", label: "Years Experience" },
    { number: "100+", label: "Local Businesses" },
    { number: "500+", label: "Security Personnel" },
    { number: "24/7", label: "Support Available" },
  ];

  const handleInputChange = (field, value) => {
    setContactForm((prev) => ({ ...prev, [field]: value }));
  };

  const handleSubmit = () => {
    if (
      !contactForm.name ||
      !contactForm.email ||
      !contactForm.phone ||
      !contactForm.message
    ) {
      message.warning("Please fill in all fields");
      return;
    }
    if (!contactForm.email.includes("@")) {
      message.warning("Please enter a valid email");
      return;
    }
    message.success("Thank you for your inquiry! We will contact you soon.");
    setContactForm({ name: "", email: "", phone: "", message: "" });
  };

  return (
    <div style={{ width: "100%", minHeight: "100vh" }}>
      <style>{`
        /* Import modern fonts */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
          font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        /* Improved typography hierarchy */
        .hero-title {
          font-size: clamp(2.5rem, 5vw, 3.5rem);
          font-weight: 700;
          line-height: 1.2;
          letter-spacing: -0.02em;
        }
        
        .section-title {
          font-size: clamp(2rem, 4vw, 2.5rem);
          font-weight: 700;
          line-height: 1.3;
          letter-spacing: -0.01em;
        }
        
        .card-title {
          font-size: clamp(1.5rem, 3vw, 2rem);
          font-weight: 600;
          line-height: 1.4;
        }
        
        .body-large {
          font-size: clamp(1rem, 2vw, 1.125rem);
          line-height: 1.6;
          font-weight: 400;
        }
        
        .body-text {
          font-size: clamp(0.875rem, 1.5vw, 1rem);
          line-height: 1.6;
          font-weight: 400;
        }
        
        .body-small {
          font-size: 0.875rem;
          line-height: 1.5;
          font-weight: 400;
        }
        
        .caption {
          font-size: 0.75rem;
          line-height: 1.4;
          font-weight: 400;
        }
        
        /* Improve text rendering */
        body {
          text-rendering: optimizeLegibility;
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
        }
        
        /* Specific improvements for sections */
        .service-card h3 {
          margin-bottom: 12px;
          color: ${colors.dark};
        }
        
        .feature-card h4 {
          margin-bottom: 8px;
          color: ${colors.dark};
        }
        
        .testimonial-text {
          font-style: italic;
          color: ${colors.text.secondary};
        }
        
        /* Contact info improvements */
        .contact-info strong {
          color: ${colors.text.primary};
          font-weight: 600;
        }
        
        /* Navigation improvements */
        .nav-link {
          font-weight: 500;
          transition: color 0.2s ease;
        }
        
        /* Button text improvements */
        .ant-btn {
          font-weight: 600;
          letter-spacing: 0.01em;
        }

        @keyframes fadeInUp {
          from { opacity: 0; transform: translateY(30px); }
          to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
          0%, 100% { transform: translateY(0px); }
          50% { transform: translateY(-10px); }
        }
        .fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .hero-illustration { animation: float 4s ease-in-out infinite; }
        
        .service-card {
          background: white;
          border-radius: 12px;
          padding: 20px;
          box-shadow: 0 4px 6px rgba(0,0,0,0.05);
          transition: all 0.3s ease;
          border: 1px solid #E5E7EB;
          height: 100%;
          position: relative;
          overflow: hidden;
        }
        .service-card::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 4px;
          background: linear-gradient(90deg, ${colors.primary}, ${colors.secondary});
        }
        .service-card:hover {
          transform: translateY(-8px);
          box-shadow: 0 20px 25px rgba(30, 64, 175, 0.15);
        }
        
        .btn-primary {
          background: ${colors.primary};
          border: none;
          color: white;
          font-weight: 600;
          height: 48px;
          padding: 0 24px;
          border-radius: 8px;
          transition: all 0.3s ease;
          box-shadow: 0 4px 6px rgba(30, 64, 175, 0.2);
          white-space: nowrap;
        }
        .btn-primary:hover {
          background: ${colors.darkBlue};
          transform: translateY(-2px);
          box-shadow: 0 6px 12px rgba(30, 64, 175, 0.3);
        }
        .btn-secondary {
          border: 2px solid ${colors.secondary};
          color: ${colors.secondary};
          background: white;
          font-weight: 600;
          height: 48px;
          padding: 0 24px;
          border-radius: 8px;
          transition: all 0.3s ease;
          white-space: nowrap;
        }
        .btn-secondary:hover {
          background: ${colors.secondary};
          color: white;
          transform: translateY(-2px);
          box-shadow: 0 6px 12px rgba(234, 88, 12, 0.3);
        }
        
        .stat-card {
          background: white;
          border-radius: 12px;
          padding: 24px 16px;
          text-align: center;
          box-shadow: 0 4px 6px rgba(0,0,0,0.05);
          border-left: 4px solid ${colors.primary};
        }
        
        .feature-card {
          background: white;
          border-radius: 12px;
          padding: 20px;
          text-align: center;
          transition: all 0.3s ease;
          border: 1px solid #E5E7EB;
          height: 100%;
        }
        .feature-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }

        /* Responsive styles */
        @media (max-width: 768px) {
          .desktop-menu {
            display: none !important;
          }
          .mobile-menu-button {
            display: block !important;
          }
          .hero-buttons {
            flex-direction: column;
            width: 100%;
          }
          .hero-buttons .ant-btn {
            width: 100%;
            margin-bottom: 12px;
          }
          .section-padding {
            padding: 60px 16px !important;
          }
          .card-content {
            padding: 16px !important;
          }
          .stats-section .ant-col {
            padding: 16px 8px !important;
          }
          .hero-section {
            margin: 8px !important;
            padding: 60px 16px !important;
            min-height: 500px !important;
          }
          
          /* Mobile typography adjustments */
          .hero-title {
            letter-spacing: -0.01em;
          }
          
          .section-title {
            letter-spacing: 0;
          }
        }
        
        @media (min-width: 769px) {
          .desktop-menu {
            display: flex !important;
          }
          .mobile-menu-button {
            display: none !important;
          }
          .section-padding {
            padding: 80px 24px !important;
          }
        }

        @media (max-width: 480px) {
          .service-card {
            margin: 0 8px;
          }
          .testimonial-card {
            margin: 0 8px;
          }
          .contact-form {
            padding: 20px 16px !important;
          }
          .stat-card {
            padding: 20px 12px;
          }
        }

        /* Ensure images are responsive */
        .hero-background {
          background-size: cover !important;
          background-position: center !important;
          background-repeat: no-repeat !important;
        }

        /* Prevent horizontal overflow */
        .ant-row {
          margin-left: 0 !important;
          margin-right: 0 !important;
        }
        
        .ant-col {
          padding-left: 8px !important;
          padding-right: 8px !important;
        }

        /* Mobile navigation improvements */
        @media (max-width: 767px) {
          .nav-container {
            padding: 0 16px !important;
          }
          .logo-text {
            font-size: 16px !important;
          }
          .logo-subtext {
            font-size: 10px !important;
          }
        }
      `}</style>

      {/* Navigation */}
      <nav
        style={{
          background: "white",
          padding: "16px 0",
          boxShadow: "0 2px 12px rgba(0,0,0,0.08)",
          position: "sticky",
          top: 0,
          zIndex: 100,
          width: "100%",
        }}
      >
        <div
          className="nav-container"
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            padding: "0 16px",
            display: "flex",
            justifyContent: "space-between",
            alignItems: "center",
            width: "100%",
            boxSizing: "border-box",
          }}
        >
          <div style={{ display: "flex", alignItems: "center", gap: "12px" }}>
            <div
              style={{
                width: "40px",
                height: "40px",
                background: `linear-gradient(135deg, ${colors.primary}, ${colors.secondary})`,
                borderRadius: "10px",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                fontSize: "18px",
                color: "white",
              }}
            >
              <FaShieldHalved />
            </div>
            <div>
              <div
                className="logo-text"
                style={{
                  fontSize: "18px",
                  fontWeight: "700",
                  color: colors.dark,
                  lineHeight: "1.2",
                  letterSpacing: "-0.01em",
                }}
              >
                ShieldTrac Security
              </div>
              <div
                className="logo-subtext"
                style={{
                  fontSize: "10px",
                  color: colors.gray,
                  lineHeight: "1.2",
                  fontWeight: "500",
                }}
              >
                Protection You Can Trust
              </div>
            </div>
          </div>

          {/* Desktop Menu */}
          <div
            className="desktop-menu"
            style={{
              display: "none",
              gap: "20px",
              alignItems: "center",
              flexWrap: "wrap",
            }}
          >
            {["About", "Services", "Why Choose Us", "Clients", "Contact"].map(
              (item) => (
                <a
                  key={item}
                  href={`#${item.toLowerCase().replace(" ", "-")}`}
                  className="nav-link"
                  style={{
                    color: colors.gray,
                    fontSize: "14px",
                    fontWeight: "500",
                    textDecoration: "none",
                    transition: "color 0.2s",
                    whiteSpace: "nowrap",
                  }}
                  onMouseEnter={(e) => (e.target.style.color = colors.primary)}
                  onMouseLeave={(e) => (e.target.style.color = colors.gray)}
                >
                  {item}
                </a>
              )
            )}
            <Button
              className="btn-primary"
              size="small"
              style={{ height: "40px", padding: "0 20px", fontWeight: "600" }}
            >
              <PhoneOutlined /> Get Quote
            </Button>
          </div>

          {/* Mobile Menu Button */}
          <div className="mobile-menu-button">
            <Button
              type="text"
              icon={mobileMenuOpen ? <CloseOutlined /> : <MenuOutlined />}
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              style={{ fontSize: "18px", padding: "4px 8px" }}
            />
          </div>
        </div>
      </nav>

      {/* Mobile Drawer */}
      <Drawer
        title={
          <div style={{ display: "flex", alignItems: "center", gap: "12px" }}>
            <div
              style={{
                width: "36px",
                height: "36px",
                background: `linear-gradient(135deg, ${colors.primary}, ${colors.secondary})`,
                borderRadius: "8px",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                fontSize: "16px",
                color: "white",
              }}
            >
              <FaShieldHalved />
            </div>
            <div>
              <div style={{ fontSize: "16px", fontWeight: "700" }}>
                ShieldTrac Security
              </div>
            </div>
          </div>
        }
        placement="right"
        onClose={() => setMobileMenuOpen(false)}
        open={mobileMenuOpen}
        bodyStyle={{ padding: "20px" }}
        headerStyle={{ borderBottom: "1px solid #f0f0f0" }}
        width={280}
      >
        <div style={{ display: "flex", flexDirection: "column", gap: "16px" }}>
          {[
            { label: "About", href: "#about" },
            { label: "Services", href: "#services" },
            { label: "Why Choose Us", href: "#why-us" },
            { label: "Clients", href: "#testimonials" },
            { label: "Contact", href: "#contact" },
          ].map((item) => (
            <a
              key={item.label}
              href={item.href}
              style={{
                color: colors.dark,
                fontSize: "16px",
                fontWeight: "500",
                textDecoration: "none",
                padding: "12px 16px",
                borderRadius: "8px",
                transition: "all 0.2s",
                border: "1px solid transparent",
              }}
              onClick={() => setMobileMenuOpen(false)}
              onMouseEnter={(e) => {
                e.target.style.background = colors.lightBlue;
                e.target.style.color = colors.primary;
                e.target.style.borderColor = colors.primary;
              }}
              onMouseLeave={(e) => {
                e.target.style.background = "transparent";
                e.target.style.color = colors.dark;
                e.target.style.borderColor = "transparent";
              }}
            >
              {item.label}
            </a>
          ))}

          <Button
            className="btn-primary"
            size="large"
            block
            style={{ marginTop: "24px" }}
            onClick={() => {
              setMobileMenuOpen(false);
              document
                .getElementById("contact")
                ?.scrollIntoView({ behavior: "smooth" });
            }}
          >
            <PhoneOutlined /> Get Quote
          </Button>
        </div>
      </Drawer>

      {/* Hero Section */}
      <div
        className="section-padding hero-section"
        style={{
          backgroundImage: `linear-gradient(135deg, #000000a2 0%, #ffffff54 80%), url(${securityImage})`,
          backgroundSize: "cover",
          backgroundPosition: "center",
          backgroundRepeat: "no-repeat",
          padding: "80px 16px",
          margin: isMobile ? "8px" : "16px",
          borderRadius: "16px",
          minHeight: "500px",
          width: "calc(100% - 16px)",
          boxSizing: "border-box",
        }}
      >
        <div
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            width: "100%",
          }}
        >
          <Row gutter={[32, 32]} align="middle">
            <Col xs={24} md={12}>
              <div className="fade-in-up" style={{ padding: "0 8px" }}>
                <div
                  style={{
                    display: "inline-block",
                    background: `${colors.lightBlue}20`,
                    padding: "8px 16px",
                    borderRadius: "20px",
                    marginBottom: "20px",
                  }}
                >
                  <span
                    className="body-small"
                    style={{
                      color: colors.secondary,
                      fontWeight: "600",
                    }}
                  >
                    🛡️ Zimbabwe Approved Security Company
                  </span>
                </div>
                <h1
                  className="hero-title"
                  style={{
                    color: colors.lightBlue,
                    marginBottom: "20px",
                  }}
                >
                  Professional{" "}
                  <span style={{ color: colors.secondary }}>Security</span>{" "}
                  Solutions for Your{" "}
                  <span style={{ color: colors.primary }}>Business</span>
                </h1>
                <p
                  className="body-large"
                  style={{
                    color: colors.lightBlue,
                    marginBottom: "32px",
                  }}
                >
                  ShieldTrac Security Services Limited is a leading
                  Zimbabwe-approved private security company. We protect
                  businesses of all sizes with the best security technologies
                  and experienced professionals.
                </p>
                <div
                  className="hero-buttons"
                  style={{
                    display: "flex",
                    gap: "12px",
                    flexWrap: "wrap",
                  }}
                >
                  <Button
                    className="btn-primary"
                    size="large"
                    onClick={() =>
                      document
                        .getElementById("contact")
                        ?.scrollIntoView({ behavior: "smooth" })
                    }
                  >
                    Get Protected <ArrowRightOutlined />
                  </Button>
                  <Button
                    className="btn-secondary"
                    size="large"
                    onClick={() =>
                      document
                        .getElementById("services")
                        ?.scrollIntoView({ behavior: "smooth" })
                    }
                  >
                    Our Services
                  </Button>
                </div>
              </div>
            </Col>
            <Col xs={24} md={12} />
          </Row>
        </div>
      </div>

      {/* Stats Section */}
      <div
        className="section-padding stats-section"
        style={{
          padding: "40px 16px",
          background: "white",
          width: "100%",
          boxSizing: "border-box",
        }}
      >
        <div
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            width: "100%",
          }}
        >
          <Row gutter={[16, 16]}>
            {stats.map((stat, index) => (
              <Col xs={12} sm={6} key={index}>
                <div className="stat-card">
                  <div
                    style={{
                      fontSize: "clamp(28px, 4vw, 36px)",
                      fontWeight: "700",
                      color: colors.primary,
                      marginBottom: "8px",
                      lineHeight: "1.2",
                    }}
                  >
                    {stat.number}
                  </div>
                  <div
                    className="body-small"
                    style={{
                      color: colors.dark,
                      fontWeight: "600",
                    }}
                  >
                    {stat.label}
                  </div>
                </div>
              </Col>
            ))}
          </Row>
        </div>
      </div>

      {/* About Section */}
      <div
        id="about"
        className="section-padding"
        style={{
          padding: "80px 16px",
          background: colors.lightOrange,
          width: "100%",
          boxSizing: "border-box",
        }}
      >
        <div
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            width: "100%",
          }}
        >
          <Row gutter={[32, 32]} align="middle">
            <Col xs={24} md={12}>
              <div style={{ padding: "0 8px" }}>
                <Tag
                  color={colors.primary}
                  style={{
                    fontSize: "14px",
                    padding: "6px 12px",
                    marginBottom: "16px",
                    fontWeight: "600",
                  }}
                >
                  ABOUT SHIELDTRAC
                </Tag>
                <h2
                  className="section-title"
                  style={{
                    color: colors.dark,
                    marginBottom: "20px",
                  }}
                >
                  Trusted Security Partner for Over a Decade
                </h2>
                <p
                  className="body-text"
                  style={{
                    color: colors.gray,
                    marginBottom: "20px",
                  }}
                >
                  ShieldTrac Security Services Limited is a leading Zimbabwe
                  approved private security company. We work to protect
                  businesses of all sizes in both private and public sectors
                  using the best security industry technologies and the best
                  experienced security guards throughout Zimbabwe.
                </p>
                <p
                  className="body-text"
                  style={{
                    color: colors.gray,
                    marginBottom: "28px",
                  }}
                >
                  We have been offering security services for over a decade with
                  good reputation in providing security services. We provide
                  trained and experienced security guards for all business
                  sectors with unmatchable quality and competitive prices.
                </p>
                <div style={{ display: "flex", gap: "12px", flexWrap: "wrap" }}>
                  <Button className="btn-primary" size="large">
                    Learn More
                  </Button>
                  <Button className="btn-secondary" size="large">
                    Our Mission
                  </Button>
                </div>
              </div>
            </Col>
            <Col xs={24} md={12}>
              <Card
                bodyStyle={{ padding: "24px 20px" }}
                style={{
                  background: "white",
                  borderRadius: "12px",
                  boxShadow: "0 8px 20px rgba(0,0,0,0.08)",
                }}
              >
                <h3
                  className="card-title"
                  style={{
                    color: colors.dark,
                    marginBottom: "20px",
                    display: "flex",
                    alignItems: "center",
                    gap: "12px",
                  }}
                >
                  <FaShieldHalved style={{ color: colors.primary }} />
                  Our Mission & Vision
                </h3>

                <div style={{ marginBottom: "24px" }}>
                  <h4
                    style={{
                      color: colors.primary,
                      marginBottom: "8px",
                      fontSize: "16px",
                      fontWeight: "600",
                    }}
                  >
                    MISSION
                  </h4>
                  <p
                    className="body-text"
                    style={{
                      color: colors.gray,
                    }}
                  >
                    To provide exceptional security services that exceed our
                    clients' expectations, while fostering a culture of trust,
                    integrity, and professionalism.
                  </p>
                </div>

                <div style={{ marginBottom: "24px" }}>
                  <h4
                    style={{
                      color: colors.secondary,
                      marginBottom: "8px",
                      fontSize: "16px",
                      fontWeight: "600",
                    }}
                  >
                    VISION
                  </h4>
                  <p
                    className="body-text"
                    style={{
                      color: colors.gray,
                    }}
                  >
                    To be the leading provider of innovative security solutions,
                    ensuring a safe and secure environment for our clients and
                    communities.
                  </p>
                </div>

                <div>
                  <h4
                    style={{
                      color: colors.dark,
                      marginBottom: "16px",
                      fontSize: "16px",
                      fontWeight: "600",
                    }}
                  >
                    OUR VALUES
                  </h4>
                  <Row gutter={[12, 12]}>
                    {[
                      "Integrity",
                      "Professionalism",
                      "Customer Focus",
                      "Trust",
                    ].map((value) => (
                      <Col xs={24} sm={12} key={value}>
                        <div
                          style={{
                            display: "flex",
                            alignItems: "center",
                            gap: "8px",
                          }}
                        >
                          <CheckCircleOutlined
                            style={{ color: colors.primary, fontSize: "14px" }}
                          />
                          <span className="body-text" style={{ fontWeight: "500" }}>
                            {value}
                          </span>
                        </div>
                      </Col>
                    ))}
                  </Row>
                </div>
              </Card>
            </Col>
          </Row>
        </div>
      </div>

      {/* Services Section */}
      <div
        id="services"
        className="section-padding"
        style={{
          padding: "80px 16px",
          background: "white",
          width: "100%",
          boxSizing: "border-box",
        }}
      >
        <div
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            width: "100%",
          }}
        >
          <div
            style={{
              textAlign: "center",
              marginBottom: "48px",
              padding: "0 8px",
            }}
          >
            <Tag
              color={colors.secondary}
              style={{
                fontSize: "14px",
                padding: "6px 12px",
                marginBottom: "16px",
                fontWeight: "600",
              }}
            >
              COMPREHENSIVE SECURITY SOLUTIONS
            </Tag>
            <h2
              className="section-title"
              style={{
                color: colors.dark,
                marginBottom: "16px",
              }}
            >
              Our Security Services
            </h2>
            <p
              className="body-large"
              style={{
                color: colors.gray,
                maxWidth: "600px",
                margin: "0 auto",
              }}
            >
              Complete security solutions tailored to protect your people,
              property, and assets
            </p>
          </div>

          <Row gutter={[16, 16]}>
            {securityServices.map((service, index) => (
              <Col xs={24} sm={12} lg={8} key={index}>
                <Card
                  className="service-card"
                  hoverable
                  bodyStyle={{ padding: "20px 16px" }}
                >
                  <div
                    style={{
                      fontSize: "40px",
                      color: colors.primary,
                      marginBottom: "16px",
                      display: "flex",
                      justifyContent: "center",
                    }}
                  >
                    {service.icon}
                  </div>
                  <h3
                    className="card-title"
                    style={{
                      textAlign: "center",
                      marginBottom: "12px",
                    }}
                  >
                    {service.title}
                  </h3>
                  <p
                    className="body-text"
                    style={{
                      color: colors.gray,
                      textAlign: "center",
                      marginBottom: "16px",
                    }}
                  >
                    {service.description}
                  </p>
                  <div>
                    {service.features.map((feature, i) => (
                      <div
                        key={i}
                        className="body-small"
                        style={{
                          display: "flex",
                          alignItems: "center",
                          gap: "8px",
                          marginBottom: "6px",
                          color: colors.primary,
                          fontWeight: "500",
                        }}
                      >
                        <CheckCircleOutlined style={{ fontSize: "12px" }} />
                        {feature}
                      </div>
                    ))}
                  </div>
                </Card>
              </Col>
            ))}
          </Row>
        </div>
      </div>

      {/* Why Choose Us Section */}
      <div
        id="why-us"
        className="section-padding"
        style={{
          padding: "80px 16px",
          background: colors.lightBlue,
          width: "100%",
          boxSizing: "border-box",
        }}
      >
        <div
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            width: "100%",
          }}
        >
          <div
            style={{
              textAlign: "center",
              marginBottom: "48px",
              padding: "0 8px",
            }}
          >
            <Tag
              color={colors.primary}
              style={{
                fontSize: "14px",
                padding: "6px 12px",
                marginBottom: "16px",
                fontWeight: "600",
              }}
            >
              WHY SHIELDTRAC
            </Tag>
            <h2
              className="section-title"
              style={{
                color: colors.dark,
                marginBottom: "16px",
              }}
            >
              Why Businesses Choose ShieldTrac
            </h2>
            <p
              className="body-large"
              style={{
                color: colors.gray,
                maxWidth: "600px",
                margin: "0 auto",
              }}
            >
              Trusted by over 100 local businesses for our exceptional security
              services and professionalism
            </p>
          </div>

          <Row gutter={[16, 16]}>
            {whyChooseUs.map((item, index) => (
              <Col xs={24} sm={12} md={8} key={index}>
                <Card
                  className="feature-card"
                  hoverable
                  bodyStyle={{ padding: "20px 16px" }}
                >
                  <div
                    style={{
                      fontSize: "40px",
                      color: colors.primary,
                      marginBottom: "16px",
                    }}
                  >
                    {item.icon}
                  </div>
                  <h4
                    style={{ 
                      marginBottom: "12px", 
                      fontSize: "16px",
                      fontWeight: "600",
                    }}
                  >
                    {item.title}
                  </h4>
                  <p
                    className="body-text"
                    style={{
                      color: colors.gray,
                    }}
                  >
                    {item.description}
                  </p>
                </Card>
              </Col>
            ))}
          </Row>
        </div>
      </div>

      {/* Testimonials Section */}
      <div
        id="testimonials"
        className="section-padding"
        style={{
          padding: "80px 16px",
          background: "white",
          width: "100%",
          boxSizing: "border-box",
        }}
      >
        <div
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            width: "100%",
          }}
        >
          <div
            style={{
              textAlign: "center",
              marginBottom: "48px",
              padding: "0 8px",
            }}
          >
            <Tag
              color={colors.secondary}
              style={{
                fontSize: "14px",
                padding: "6px 12px",
                marginBottom: "16px",
                fontWeight: "600",
              }}
            >
              TRUSTED BY BUSINESSES
            </Tag>
            <h2
              className="section-title"
              style={{
                color: colors.dark,
                marginBottom: "16px",
              }}
            >
              What Our Clients Say
            </h2>
            <p
              className="body-large"
              style={{
                color: colors.gray,
                maxWidth: "600px",
                margin: "0 auto",
              }}
            >
              Don't just take our word for it - hear from some of our satisfied
              clients across various industries
            </p>
          </div>

          <Row gutter={[16, 16]}>
            {testimonials.map((testimonial, index) => (
              <Col xs={24} md={12} key={index}>
                <Card
                  className="testimonial-card"
                  bodyStyle={{ padding: "24px 20px" }}
                  style={{
                    background: colors.lightGray,
                    borderRadius: "12px",
                    height: "100%",
                    borderLeft: `4px solid ${colors.primary}`,
                  }}
                >
                  <div
                    style={{
                      fontSize: "32px",
                      color: colors.primary,
                      marginBottom: "16px",
                    }}
                  >
                    ❝
                  </div>
                  <p
                    className="testimonial-text body-text"
                    style={{
                      marginBottom: "20px",
                    }}
                  >
                    "{testimonial.content}"
                  </p>
                  <div>
                    <div
                      className="body-text"
                      style={{
                        fontWeight: "600",
                        marginBottom: "4px",
                      }}
                    >
                      {testimonial.name}
                    </div>
                    <div
                      className="body-small"
                      style={{
                        color: colors.primary,
                        fontWeight: "500",
                        marginBottom: "4px",
                      }}
                    >
                      {testimonial.company}
                    </div>
                    <div
                      className="caption"
                      style={{ 
                        color: colors.text.secondary,
                      }}
                    >
                      {testimonial.role}
                    </div>
                  </div>
                </Card>
              </Col>
            ))}
          </Row>
        </div>
      </div>

      {/* CTA Section */}
      <div
        className="section-padding"
        style={{
          padding: "80px 16px",
          background: `radial-gradient(circle at center, ${colors.primary}, ${colors.darkBlue})`,
          width: "100%",
          boxSizing: "border-box",
        }}
      >
        <div
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            width: "100%",
            textAlign: "center",
          }}
        >
          <h2
            className="section-title"
            style={{
              color: "white",
              marginBottom: "16px",
            }}
          >
            Ready to Secure Your Business?
          </h2>
          <p
            className="body-large"
            style={{
              color: "rgba(255,255,255,0.9)",
              maxWidth: "600px",
              margin: "0 auto 32px",
            }}
          >
            Contact us today for a free security assessment and customized quote
          </p>
          <div
            style={{
              display: "flex",
              gap: "12px",
              flexWrap: "wrap",
              justifyContent: "center",
            }}
          >
            <Button
              size="large"
              style={{
                background: colors.secondary,
                border: "none",
                color: "white",
                fontWeight: "600",
                height: "48px",
                padding: "0 24px",
                borderRadius: "8px",
              }}
              onClick={() =>
                document
                  .getElementById("contact")
                  ?.scrollIntoView({ behavior: "smooth" })
              }
            >
              <PhoneOutlined /> Get Free Quote
            </Button>
            <Button
              size="large"
              style={{
                background: "transparent",
                border: "2px solid white",
                color: "white",
                fontWeight: "600",
                height: "48px",
                padding: "0 24px",
                borderRadius: "8px",
              }}
            >
              <MailOutlined /> Email Us
            </Button>
          </div>
        </div>
      </div>

      {/* Contact Section */}
      <div
        id="contact"
        className="section-padding"
        style={{
          padding: "80px 16px",
          background: colors.lightGray,
          width: "100%",
          boxSizing: "border-box",
        }}
      >
        <div
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            width: "100%",
          }}
        >
          <Row gutter={[32, 32]}>
            <Col xs={24} md={12}>
              <div style={{ padding: "0 8px" }}>
                <Tag
                  color={colors.primary}
                  style={{
                    fontSize: "14px",
                    padding: "6px 12px",
                    marginBottom: "16px",
                    fontWeight: "600",
                  }}
                >
                  GET IN TOUCH
                </Tag>
                <h2
                  className="section-title"
                  style={{
                    color: colors.dark,
                    marginBottom: "16px",
                  }}
                >
                  Contact ShieldTrac Security
                </h2>
                <p
                  className="body-large"
                  style={{
                    color: colors.gray,
                    marginBottom: "32px",
                  }}
                >
                  Ready to enhance your security? Our team is here to answer
                  your questions and provide customized security solutions for
                  your business.
                </p>

                <div
                  className="contact-info"
                  style={{
                    display: "flex",
                    flexDirection: "column",
                    gap: "20px",
                  }}
                >
                  {[
                    {
                      icon: <PhoneOutlined />,
                      title: "Phone",
                      details: ["+263 78 809 4724", "+263 71 273 4834", "Landline: 242328474/5"],
                    },
                    {
                      icon: <WhatsAppOutlined style={{ color: "#25D366" }} />,
                      title: "WhatsApp",
                      details: ["+263 78 238 1986", "+263 77 593 4109"],
                      whatsapp: true,
                    },
                    {
                      icon: <MailOutlined />,
                      title: "Email",
                      details: ["shieldtrac@gmail.co.zw", "Info@shieldtrac.co.zw", "richard@shieldtrac.co.zw"],
                    },
                    {
                      icon: <EnvironmentOutlined />,
                      title: "Address",
                      details: ["26 Connaught Avondale", "Harare, Zimbabwe"],
                    },
                  ].map((contact, index) => (
                    <div
                      key={index}
                      style={{
                        display: "flex",
                        alignItems: "flex-start",
                        gap: "16px",
                      }}
                    >
                      <div
                        style={{
                          width: "48px",
                          height: "48px",
                          background: colors.primary,
                          borderRadius: "50%",
                          display: "flex",
                          alignItems: "center",
                          justifyContent: "center",
                          color: "white",
                          fontSize: "18px",
                          flexShrink: 0,
                        }}
                      >
                        {contact.icon}
                      </div>

                      <div>
                        <span
                          className="body-small"
                          style={{ 
                            color: colors.text.secondary,
                            display: "block",
                            marginBottom: "4px",
                          }}
                        >
                          {contact.title}
                        </span>

                        {contact.details.map((detail, i) => {
                          const cleaned = detail.replace(/\D/g, "");
                          const isWhatsapp = contact.whatsapp;

                          return (
                            <div
                              key={i}
                              className="body-text"
                              style={{
                                display: "block",
                                lineHeight: "1.4",
                                fontWeight: detail.includes("Landline") ? "600" : "500",
                                color: detail.includes("Landline") ? colors.primary : colors.text.primary,
                              }}
                            >
                              {isWhatsapp ? (
                                <a
                                  href={`https://wa.me/${cleaned}`}
                                  target="_blank"
                                  rel="noopener noreferrer"
                                  style={{ color: colors.text.primary }}
                                >
                                  <WhatsAppOutlined
                                    style={{
                                      marginRight: 6,
                                      color: "#25D366",
                                    }}
                                  />
                                  {detail}
                                </a>
                              ) : detail.includes("@") ? (
                                <a
                                  href={`mailto:${detail}`}
                                  style={{ 
                                    color: detail.includes("Landline") ? colors.primary : colors.primary,
                                    textDecoration: "none"
                                  }}
                                >
                                  {detail}
                                </a>
                              ) : (
                                detail
                              )}
                            </div>
                          );
                        })}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </Col>

            <Col xs={24} md={12}>
              <Card
                className="contact-form"
                bodyStyle={{ padding: "24px 20px" }}
                style={{
                  background: "white",
                  borderRadius: "12px",
                  boxShadow: "0 8px 20px rgba(0,0,0,0.08)",
                }}
              >
                <h3
                  className="card-title"
                  style={{ marginBottom: "24px" }}
                >
                  Send us a Message
                </h3>
                <div
                  style={{
                    display: "flex",
                    flexDirection: "column",
                    gap: "16px",
                  }}
                >
                  <Input
                    size="large"
                    placeholder="Full Name"
                    value={contactForm.name}
                    onChange={(e) => handleInputChange("name", e.target.value)}
                    style={{ borderRadius: "8px" }}
                  />
                  <Input
                    size="large"
                    placeholder="Email Address"
                    value={contactForm.email}
                    onChange={(e) => handleInputChange("email", e.target.value)}
                    style={{ borderRadius: "8px" }}
                  />
                  <Input
                    size="large"
                    placeholder="Phone Number"
                    value={contactForm.phone}
                    onChange={(e) => handleInputChange("phone", e.target.value)}
                    style={{ borderRadius: "8px" }}
                  />
                  <TextArea
                    rows={5}
                    placeholder="How can we help you?"
                    value={contactForm.message}
                    onChange={(e) =>
                      handleInputChange("message", e.target.value)
                    }
                    style={{ borderRadius: "8px", resize: "vertical" }}
                  />
                  <Button
                    className="btn-primary"
                    size="large"
                    onClick={handleSubmit}
                    style={{ width: "100%", height: "48px" }}
                  >
                    Send Message <ArrowRightOutlined />
                  </Button>
                </div>
              </Card>
            </Col>
          </Row>
        </div>
      </div>

      {/* Footer */}
      <div
        style={{
          background: colors.dark,
          color: "white",
          padding: "48px 16px 32px",
          width: "100%",
          boxSizing: "border-box",
        }}
      >
        <div
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            width: "100%",
          }}
        >
          <Row gutter={[32, 32]}>
            <Col xs={24} md={8}>
              <div style={{ marginBottom: "24px" }}>
                <div
                  style={{
                    display: "flex",
                    alignItems: "center",
                    gap: "12px",
                    marginBottom: "16px",
                  }}
                >
                  <div
                    style={{
                      width: "40px",
                      height: "40px",
                      background: `linear-gradient(135deg, ${colors.primary}, ${colors.secondary})`,
                      borderRadius: "10px",
                      display: "flex",
                      alignItems: "center",
                      justifyContent: "center",
                      fontSize: "18px",
                      color: "white",
                    }}
                  >
                    <FaShieldHalved />
                  </div>
                  <div>
                    <div
                      className="body-large"
                      style={{
                        color: "white",
                        fontWeight: "700",
                        marginBottom: "2px",
                      }}
                    >
                      ShieldTrac Security
                    </div>
                    <div
                      className="caption"
                      style={{ 
                        color: "rgba(255,255,255,0.7)",
                        fontWeight: "500",
                      }}
                    >
                      Protection You Can Trust
                    </div>
                  </div>
                </div>
                <p
                  className="body-small"
                  style={{
                    opacity: 0.8,
                    marginBottom: "24px",
                    color: "white",
                  }}
                >
                  Zimbabwe's leading private security company providing
                  comprehensive security solutions for businesses of all sizes.
                </p>
                <div style={{ display: "flex", gap: "12px" }}>
                  <div
                    style={{
                      width: "36px",
                      height: "36px",
                      background: colors.primary,
                      borderRadius: "8px",
                      display: "flex",
                      alignItems: "center",
                      justifyContent: "center",
                      cursor: "pointer",
                    }}
                  >
                    <PhoneOutlined />
                  </div>
                  <div
                    style={{
                      width: "36px",
                      height: "36px",
                      background: colors.primary,
                      borderRadius: "8px",
                      display: "flex",
                      alignItems: "center",
                      justifyContent: "center",
                      cursor: "pointer",
                    }}
                  >
                    <MailOutlined />
                  </div>
                </div>
              </div>
            </Col>

            <Col xs={24} md={8}>
              <h4
                style={{
                  color: "white",
                  marginBottom: "20px",
                  fontSize: "16px",
                  fontWeight: "600",
                }}
              >
                Quick Links
              </h4>
              <div
                style={{
                  display: "flex",
                  flexDirection: "column",
                  gap: "12px",
                }}
              >
                {[
                  "About Us",
                  "Services",
                  "Why Choose Us",
                  "Testimonials",
                  "Contact",
                ].map((item) => (
                  <a
                    key={item}
                    href={`#${item.toLowerCase().replace(" ", "-")}`}
                    style={{
                      color: "rgba(255,255,255,0.8)",
                      textDecoration: "none",
                      fontSize: "14px",
                      transition: "color 0.2s",
                    }}
                    onMouseEnter={(e) => (e.target.style.color = "white")}
                    onMouseLeave={(e) =>
                      (e.target.style.color = "rgba(255,255,255,0.8)")
                    }
                  >
                    {item}
                  </a>
                ))}
              </div>
            </Col>

            <Col xs={24} md={8}>
              <h4
                style={{
                  color: "white",
                  marginBottom: "20px",
                  fontSize: "16px",
                  fontWeight: "600",
                }}
              >
                Our Services
              </h4>
              <div
                style={{
                  display: "flex",
                  flexDirection: "column",
                  gap: "12px",
                }}
              >
                {securityServices.map((service) => (
                  <a
                    key={service.title}
                    href="#services"
                    style={{
                      color: "rgba(255,255,255,0.8)",
                      textDecoration: "none",
                      fontSize: "14px",
                      transition: "color 0.2s",
                    }}
                    onMouseEnter={(e) => (e.target.style.color = "white")}
                    onMouseLeave={(e) =>
                      (e.target.style.color = "rgba(255,255,255,0.8)")
                    }
                  >
                    {service.title}
                  </a>
                ))}
              </div>
            </Col>
          </Row>

          <Divider
            style={{
              background: "rgba(255,255,255,0.2)",
              margin: "32px 0 24px",
            }}
          />

          <div
            style={{
              display: "flex",
              flexDirection: isMobile ? "column" : "row",
              justifyContent: "space-between",
              alignItems: isMobile ? "flex-start" : "center",
              gap: "16px",
            }}
          >
            <p
              className="body-small"
              style={{
                opacity: 0.8,
                color: "white",
              }}
            >
              © 2024 ShieldTrac Security Services Limited. All rights reserved.
            </p>
            <div style={{ display: "flex", gap: "24px" }}>
              <a
                href="#"
                style={{
                  color: "rgba(255,255,255,0.8)",
                  textDecoration: "none",
                  fontSize: "14px",
                }}
              >
                Privacy Policy
              </a>
              <a
                href="#"
                style={{
                  color: "rgba(255,255,255,0.8)",
                  textDecoration: "none",
                  fontSize: "14px",
                }}
              >
                Terms of Service
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ShieldTracLanding;