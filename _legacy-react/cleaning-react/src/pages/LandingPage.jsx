import React, { useState, useEffect, useRef } from "react";
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
  EnvironmentFilled,
  BugFilled,
  WhatsAppOutlined,
} from "@ant-design/icons";
import mouseData from "../assets/animations/mouse.json";
import cleaningImage from "/cleaning_lady.png";
import officeImage from "/office_cleaning.png";

const { TextArea } = Input;
const { Text, Title } = Typography;
const OnpointLanding = () => {
  const [scrollY, setScrollY] = useState(0);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [contactForm, setContactForm] = useState({
    name: "",
    email: "",
    phone: "",
    message: "",
  });
  const lottieRef = useRef();

  useEffect(() => {
    // Set playback speed to 2x after the animation loads
    lottieRef.current.setSpeed(0.5);
  }, []);

  useEffect(() => {
    const handleScroll = () => setScrollY(window.scrollY);
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  // Color scheme
  const colors = {
    primary: "#68A77C", // Green
    secondary: "#FF8C00", // Orange
    lightGreen: "#F0F9F4",
    lightOrange: "#FFF4E6",
    dark: "#1F2937",
    gray: "#6B7280",
    lightGray: "#F8FAFC",
  };

  // Services from the document
  const cleaningServices = [
    {
      title: "Daily Contract Cleaning",
      icon: "🧹",
      description:
        "Regular maintenance cleaning for offices, homes, and commercial spaces",
      features: ["Daily maintenance", "Office cleaning", "Commercial spaces"],
    },
    {
      title: "Industrial Deep Cleaning",
      icon: "🏭",
      description:
        "Comprehensive industrial cleaning for factories and manufacturing plants",
      features: [
        "Factory cleaning",
        "Manufacturing plants",
        "Heavy-duty cleaning",
      ],
    },
    {
      title: "Carpet & Upholstery Cleaning",
      icon: "🧽",
      description: "Deep cleaning for carpets, rugs, and furniture upholstery",
      features: ["Carpet deep clean", "Upholstery cleaning", "Stain removal"],
    },
    {
      title: "Window Cleaning",
      icon: "🪟",
      description:
        "Professional window cleaning including high-rise rope access",
      features: ["High-rise windows", "Rope access", "Crystal clear results"],
    },
    {
      title: "Pre & Post Occupancy Cleaning",
      icon: "🏠",
      description: "Complete cleaning before moving in or after moving out",
      features: [
        "Move-in cleaning",
        "Move-out cleaning",
        "Property preparation",
      ],
    },
    {
      title: "Floor Surface Treatment",
      icon: "💎",
      description:
        "Slip resistance treatment and hard surface floor maintenance",
      features: ["Slip resistance", "Floor polishing", "Surface protection"],
    },
    {
      title: "Cleaning Chemicals & Fumigation",
      icon: "🧪",
      description:
        "Professional cleaning chemicals and integrated fumigation services",
      features: [
        "Eco-friendly chemicals",
        "Integrated solutions",
        "Quality products",
      ],
    },
    {
      title: "Sanitary Disposal",
      icon: "🚮",
      description: "Safe and hygienic waste disposal services",
      features: [
        "Waste management",
        "Hygienic disposal",
        "Compliance certified",
      ],
    },
    {
      title: "Basement & Parking Cleaning",
      icon: "🅿️",
      description: "Specialized cleaning for basements and parking facilities",
      features: ["Basement cleaning", "Parking bays", "Large area cleaning"],
    },
    {
      title: "Flood Extraction & Drying",
      icon: "💧",
      description: "Emergency flood water extraction and drying services",
      features: ["Emergency response", "Water extraction", "Drying equipment"],
    },
  ];

  const hygienicServices = [
    {
      title: "Restroom Sanitation",
      description: "Complete restroom hygiene solutions and dispensers",
      icon: "🚻",
    },
    {
      title: "Sanitation Consumables",
      description: "Supply of hygiene products and consumables",
      icon: "🧴",
    },
    {
      title: "Dispenser Services",
      description: "Installation and maintenance of hygiene dispensers",
      icon: "⚙️",
    },
    {
      title: "Workplace Hygiene",
      description: "Complete workplace hygiene management",
      icon: "🏢",
    },
  ];

  const pestControlServices = [
    {
      title: "Cockroach Control",
      description: "Complete elimination of cockroach infestations",
      icon: "🐜",
    },
    {
      title: "Flying Insect Control",
      description: "Control of flies, mosquitoes, and other flying insects",
      icon: "🦟",
    },
    {
      title: "Ant & Slug Elimination",
      description: "Targeted solutions for ants and slugs",
      icon: "🐌",
    },
    {
      title: "Bee & Wasp Removal",
      description: "Safe removal of bees, wasps, and wood bees",
      icon: "🐝",
    },
    {
      title: "Termite Control",
      description: "Comprehensive termite prevention and elimination",
      icon: "🪳",
    },
    {
      title: "Rodent Control",
      description: "Complete rodent management solutions",
      icon: "🐀",
    },
  ];

  const testimonials = [
    {
      name: "Mr. Innocent Murisa",
      company: "Sterkinekor Movies",
      role: "Operations Manager",
      content:
        "Professional service with exceptional attention to detail across all our branches. Highly recommended!",
      phone: "+263 772 429 750",
    },
    {
      name: "Mr. Pride",
      company: "Simbisa Branch (Chicken Inn)",
      role: "Operations Manager",
      content:
        "Reliable and thorough cleaning services that maintain our high standards of hygiene.",
      phone: "+263 77 227 1231",
    },
    {
      name: "Mr. Adrian",
      company: "All the Way Restaurant",
      role: "Operations Manager",
      content:
        "Their fumigation services have completely eliminated our pest problems. Excellent work!",
      phone: "+2774955865",
    },
    {
      name: "Mrs. Kim",
      company: "Café Nush",
      role: "Operations Manager",
      content:
        "The hygienic services have transformed our restroom facilities. Our customers have noticed the difference.",
      phone: "+263 78 869 6249",
    },
  ];

  const whyChooseUs = [
    {
      icon: <TeamOutlined />,
      title: "Highly Professional Staff",
      description: "Our team consists of trained and experienced professionals",
    },
    {
      icon: <SafetyCertificateOutlined />,
      title: "100% Satisfaction Guarantee",
      description:
        "We stand behind our work with a complete satisfaction guarantee",
    },
    {
      icon: <CrownOutlined />,
      title: "Quality Control System",
      description:
        "Rigorous quality checks ensure consistent excellent service",
    },
    {
      icon: <EnvironmentFilled />,
      title: "Eco-Friendly Solutions",
      description:
        "Environmentally responsible pest control and cleaning methods",
    },
    {
      icon: <CheckCircleOutlined />,
      title: "Compliance Certified",
      description:
        "Fully licensed and insured with all necessary certifications",
    },
    {
      icon: <PhoneOutlined />,
      title: "24/7 Emergency Service",
      description:
        "Round-the-clock availability for urgent cleaning and pest control needs",
    },
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
    message.success("Thank you! We will contact you soon.");
    setContactForm({ name: "", email: "", phone: "", message: "" });
  };

  return (
    <div style={{ minHeight: "100vh" }}>
      <style>{`
        @keyframes fadeInUp {
          from { opacity: 0; transform: translateY(30px); }
          to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
          0%, 100% { transform: translateY(0px); }
          50% { transform: translateY(-15px); }
        }
        .fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .hero-illustration { animation: float 3s ease-in-out infinite; }
        
        .service-card {
          background: white;
          border-radius: 12px;
          padding: 16px 8px;
          box-shadow: 0 2px 12px rgba(0,0,0,0.05);
          transition: all 0.3s ease;
          border: 1px solid #F0F0F0;
          height: 100%;
        }
        .service-card:hover {
          transform: translateY(-8px);
          box-shadow: 0 12px 24px rgba(104, 167, 124, 0.15);
        }
        
        .btn-primary {
          background: ${colors.primary};
          border: none;
          color: white;
          font-weight: 600;
          height: 48px;
          padding: 0 32px;
          border-radius: 8px;
          transition: all 0.3s ease;
        }
        .btn-primary:hover {
          background: #5a9169;
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(104, 167, 124, 0.3);
        }
        .btn-secondary {
          border: 2px solid ${colors.secondary};
          color: ${colors.secondary};
          background: white;
          font-weight: 600;
          height: 48px;
          padding: 0 32px;
          border-radius: 8px;
          transition: all 0.3s ease;
        }
        .btn-secondary:hover {
          background: ${colors.secondary};
          color: white;
          transform: translateY(-2px);
        }
        
        .mobile-menu {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100vh;
          background: white;
          z-index: 1000;
          padding: 24px;
          transform: translateX(-100%);
          transition: transform 0.3s ease;
        }
        .mobile-menu.open {
          transform: translateX(0);
        }
      `}</style>

      {/* Navigation */}
      <nav
        style={{
          background: "white",
          padding: "16px 0",
          boxShadow: "0 2px 8px rgba(0,0,0,0.04)",
          position: "sticky",
          top: 0,
          zIndex: 100,
          width: "100%",
        }}
      >
        <div
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
                width: "42px",
                height: "42px",
                background: `linear-gradient(135deg, ${colors.primary}, ${colors.secondary})`,
                borderRadius: "10px",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                fontSize: "20px",
                color: "white",
              }}
            >
              ✨
            </div>
            <div>
              <div
                style={{
                  fontSize: "18px",
                  fontWeight: "700",
                  color: colors.dark,
                  lineHeight: "1.2",
                }}
              >
                Onpoint Hygiene & Fumigation Services
              </div>
              <div style={{ fontSize: "10px", color: colors.gray }}>
                Consistency in Excellence
              </div>
            </div>
          </div>

          {/* Desktop Menu */}
          <div
            className="desktop-menu"
            style={{
              display: "none",
              gap: "24px",
              alignItems: "center",
              flexWrap: "wrap",
            }}
          >
            {["About", "Services", "Pest Control", "Clients", "Contact"].map(
              (item) => (
                <a
                  key={item}
                  href={`#${item.toLowerCase().replace(" ", "-")}`}
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
              style={{ height: "40px", padding: "0 20px" }}
            >
              <PhoneOutlined /> Book Now
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

      {/* Add responsive CSS */}
      <style>{`
        @media (min-width: 768px) {
          .desktop-menu {
            display: flex !important;
          }
          .mobile-menu-button {
            display: none !important;
          }
        }
        
        @media (max-width: 767px) {
          .desktop-menu {
            display: none !important;
          }
          .mobile-menu-button {
            display: block !important;
          }
        }
      `}</style>

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
              ✨
            </div>
            <div>
              <div style={{ fontSize: "16px", fontWeight: "700" }}>
                Onpoint Hygiene & Fumigation Services
              </div>
              <div style={{ fontSize: "11px", color: colors.gray }}>
                Cleaning Services
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
            { label: "Pest Control", href: "#pest-control" },
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
                e.target.style.background = colors.lightGreen;
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
            <PhoneOutlined /> Book Now
          </Button>
        </div>
      </Drawer>

      {/* Hero Section */}
      <div
        className="section-padding"
        style={{
          padding: "80px 16px 120px",
          position: "relative",
          overflow: "hidden",
          width: "100%",
          boxSizing: "border-box",
        }}
      >
        <div
          style={{
            maxWidth: "1200px",
            margin: "0 auto",
            position: "relative",
            zIndex: 1,
            width: "100%",
          }}
        >
          <Row gutter={[24, 24]} align="middle">
            <Col xs={24} md={12}>
              <div
                className="fade-in-up"
                style={{
                  padding: "0 8px",
                  textAlign: { xs: "center", md: "left" },
                }}
              >
                <div
                  style={{
                    display: "inline-block",
                    background: `${colors.primary}20`,
                    padding: "8px 16px",
                    borderRadius: "20px",
                    marginBottom: "24px",
                  }}
                >
                  <span
                    style={{
                      color: colors.primary,
                      fontSize: "14px",
                      fontWeight: "600",
                    }}
                  >
                    ✨ Internationally Recognized
                  </span>
                </div>
                <Title
                  level={1}
                  style={{
                    fontSize: "clamp(32px, 5vw, 56px)",
                    fontWeight: "800",
                    color: colors.dark,
                    lineHeight: "1.1",
                    marginBottom: "24px",
                  }}
                >
                  Professional{" "}
                  <span style={{ color: colors.primary }}>Cleaning</span> &{" "}
                  <span style={{ color: colors.secondary }}>Pest Control</span>{" "}
                  Services
                </Title>
                <p
                  style={{
                    fontSize: "clamp(16px, 2vw, 18px)",
                    color: colors.gray,
                    lineHeight: "1.7",
                    marginBottom: "40px",
                    maxWidth: "520px",
                    marginLeft: "auto",
                    marginRight: "auto",
                  }}
                >
                  Onpoint Hygiene and Fumigation Services offers comprehensive
                  cleaning and pest control solutions across Africa. With
                  state-of-the-art equipment and certified professionals, we
                  deliver excellence in every service.
                </p>
                <div
                  className="hero-buttons"
                  style={{
                    display: "flex",
                    gap: "16px",
                    flexWrap: "wrap",
                    justifyContent: { xs: "center", md: "flex-start" },
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
                    Book Appointment <ArrowRightOutlined />
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
                    View Services
                  </Button>
                </div>
              </div>
            </Col>
            <Col xs={24} md={12}>
              <div
                style={{
                  textAlign: "center",
                  padding: "0 8px",
                }}
              >
                <img
                  style={{
                    width: "100%",
                    maxWidth: "500px",
                    height: "auto",
                  }}
                  src={cleaningImage}
                  alt="Professional cleaning service"
                />
              </div>
            </Col>
          </Row>
        </div>
      </div>

      {/* Stats Section */}
      <div
        className="section-padding stats-section"
        style={{
          padding: "60px 16px",
          background: colors.primary,
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
          <Row gutter={[16, 16]} style={{ textAlign: "center" }}>
            {[
              { number: "50+", text: "Professional Staff", color: "white" },
              {
                number: "1000+",
                text: "Satisfied Clients",
                color: colors.secondary,
              },
              { number: "24/7", text: "Emergency Service", color: "white" },
              {
                number: "100%",
                text: "Satisfaction Guarantee",
                color: colors.secondary,
              },
            ].map((stat, index) => (
              <Col xs={12} md={6} key={index}>
                <div style={{ padding: "16px 8px" }}>
                  <div
                    style={{
                      fontSize: "clamp(32px, 4vw, 42px)",
                      fontWeight: "700",
                      color: stat.color,
                      marginBottom: "8px",
                      lineHeight: "1.2",
                    }}
                  >
                    {stat.number}
                  </div>
                  <div
                    style={{
                      fontSize: "14px",
                      color: colors.lightGray,
                      fontWeight: "600",
                    }}
                  >
                    {stat.text}
                  </div>
                </div>
              </Col>
            ))}
          </Row>
        </div>
      </div>

      {/* Cleaning Services Section */}
      <div
        id="services"
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
              }}
            >
              COMPREHENSIVE CLEANING SOLUTIONS
            </Tag>
            <Title
              level={2}
              style={{
                fontSize: "clamp(28px, 4vw, 42px)",
                fontWeight: "700",
                color: colors.dark,
                marginBottom: "16px",
              }}
            >
              Professional Cleaning Services
            </Title>
            <p
              style={{
                fontSize: "clamp(14px, 2vw, 16px)",
                color: colors.gray,
                maxWidth: "600px",
                margin: "0 auto",
                lineHeight: "1.6",
              }}
            >
              From daily maintenance to specialized industrial cleaning, we
              offer complete solutions for all your cleaning needs
            </p>
          </div>

          <Row gutter={[16, 16]}>
            {cleaningServices.map((service, index) => (
              <Col xs={24} sm={12} lg={8} xl={6} key={index}>
                <Card
                  className="service-card"
                  hoverable
                  bodyStyle={{ padding: "16px 12px" }}
                  cover={
                    <div
                      style={{
                        fontSize: "48px",
                        textAlign: "center",
                        padding: "20px 0",
                      }}
                    >
                      {service.icon}
                    </div>
                  }
                >
                  <div className="card-content">
                    <Text
                      strong
                      style={{
                        fontSize: "16px",
                        color: colors.dark,
                        marginBottom: "8px",
                        display: "block",
                      }}
                    >
                      {service.title}
                    </Text>
                    <p
                      style={{
                        color: colors.gray,
                        marginBottom: "16px",
                        fontSize: "14px",
                        lineHeight: "1.5",
                      }}
                    >
                      {service.description}
                    </p>
                    <div>
                      {service.features.map((feature, i) => (
                        <div
                          key={i}
                          style={{
                            display: "flex",
                            alignItems: "center",
                            gap: "8px",
                            marginBottom: "6px",
                            fontSize: "13px",
                            color: colors.primary,
                          }}
                        >
                          <CheckCircleOutlined style={{ fontSize: "12px" }} />
                          {feature}
                        </div>
                      ))}
                    </div>
                  </div>
                </Card>
              </Col>
            ))}
          </Row>
        </div>
      </div>

      {/* Hygienic Services Section */}
      <div
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
          <Row gutter={[24, 24]} align="middle">
            <Col xs={24} md={12}>
              <div style={{ padding: "0 8px" }}>
                <Tag
                  color={colors.secondary}
                  style={{
                    fontSize: "14px",
                    padding: "6px 12px",
                    marginBottom: "16px",
                  }}
                >
                  HYGIENIC SOLUTIONS
                </Tag>
                <Title
                  level={2}
                  style={{
                    fontSize: "clamp(28px, 4vw, 42px)",
                    fontWeight: "700",
                    color: colors.dark,
                    marginBottom: "16px",
                  }}
                >
                  Complete Restroom & Workplace Hygiene
                </Title>
                <p
                  style={{
                    fontSize: "clamp(14px, 2vw, 16px)",
                    color: colors.gray,
                    lineHeight: "1.7",
                    marginBottom: "32px",
                  }}
                >
                  We provide comprehensive hygienic services including restroom
                  sanitation, dispenser services, and consumables to maintain a
                  healthy environment for your workforce.
                </p>

                <Row gutter={[12, 12]}>
                  {hygienicServices.map((service, index) => (
                    <Col xs={24} sm={12} key={index}>
                      <Card
                        bodyStyle={{ padding: "16px" }}
                        style={{
                          height: "100%",
                          background: colors.lightGreen,
                          borderRadius: "12px",
                        }}
                      >
                        <div
                          style={{
                            fontSize: "32px",
                            marginBottom: "12px",
                          }}
                        >
                          {service.icon}
                        </div>
                        <div>
                          <h4
                            style={{
                              fontSize: "16px",
                              fontWeight: "600",
                              color: colors.dark,
                              marginBottom: "4px",
                            }}
                          >
                            {service.title}
                          </h4>
                          <p
                            style={{
                              fontSize: "14px",
                              color: colors.gray,
                              margin: 0,
                              lineHeight: "1.5",
                            }}
                          >
                            {service.description}
                          </p>
                        </div>
                      </Card>
                    </Col>
                  ))}
                </Row>
              </div>
            </Col>
            <Col xs={24} md={12}>
              <div style={{ padding: "0 8px", textAlign: "center" }}>
                <img
                  style={{
                    width: "100%",
                    maxWidth: "500px",
                    height: "auto",
                  }}
                  src={officeImage}
                  alt="Office cleaning services"
                />
              </div>
            </Col>
          </Row>
        </div>
      </div>

      {/* Pest Control Section */}
      <div
        id="pest-control"
        className="section-padding"
        style={{
          padding: "80px 16px",
          background: colors.primary,
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
              }}
            >
              PEST CONTROL SOLUTIONS
            </Tag>
            <Title
              level={2}
              style={{
                fontSize: "clamp(28px, 4vw, 42px)",
                fontWeight: "700",
                color: "white",
                marginBottom: "16px",
              }}
            >
              Environmentally Friendly Pest Control
            </Title>
            <p
              style={{
                fontSize: "clamp(14px, 2vw, 16px)",
                color: colors.lightGray,
                maxWidth: "600px",
                margin: "0 auto",
                lineHeight: "1.6",
              }}
            >
              Fully licensed and insured pest control services that are safe for
              your family, pets, and the environment
            </p>
          </div>

          <Row gutter={[16, 16]}>
            {pestControlServices.map((service, index) => (
              <Col xs={24} sm={12} md={8} key={index}>
                <Card
                  bodyStyle={{ padding: "20px 16px", textAlign: "center" }}
                  style={{
                    borderRadius: 12,
                    height: "100%",
                  }}
                  hoverable
                >
                  <div
                    style={{
                      fontSize: "48px",
                      marginBottom: "16px",
                    }}
                  >
                    {service.icon}
                  </div>
                  <h3
                    style={{
                      fontSize: "18px",
                      fontWeight: "600",
                      color: colors.dark,
                      marginBottom: "8px",
                    }}
                  >
                    {service.title}
                  </h3>
                  <p
                    style={{
                      fontSize: "14px",
                      color: colors.gray,
                      lineHeight: "1.4",
                      margin: 0,
                    }}
                  >
                    {service.description}
                  </p>
                </Card>
              </Col>
            ))}
          </Row>

          {/* Why Choose Our Pest Control */}
          <div style={{ marginTop: "60px" }}>
            <Row gutter={[24, 24]}>
              <Col xs={24} lg={12}>
                <div style={{ padding: "0 8px" }}>
                  <Title
                    level={3}
                    style={{
                      fontSize: "clamp(24px, 3vw, 28px)",
                      fontWeight: "700",
                      color: "white",
                      marginBottom: "24px",
                    }}
                  >
                    Why Choose Our Eco-Friendly Pest Control?
                  </Title>
                  <p
                    style={{
                      fontSize: "16px",
                      color: colors.lightGray,
                      lineHeight: "1.7",
                      marginBottom: "32px",
                    }}
                  >
                    Our environmentally responsible approach makes sense for
                    your family, pets, and the planet. We use solutions that
                    protect the air we breathe and water we drink.
                  </p>
                  <div
                    style={{
                      display: "flex",
                      flexDirection: "column",
                      gap: "20px",
                    }}
                  >
                    {whyChooseUs.map((item, index) => (
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
                            color: colors.secondary,
                            fontSize: "20px",
                            marginTop: "2px",
                            flexShrink: 0,
                          }}
                        >
                          {item.icon}
                        </div>
                        <div>
                          <h4
                            style={{
                              fontSize: "16px",
                              fontWeight: "600",
                              color: "white",
                              marginBottom: "4px",
                            }}
                          >
                            {item.title}
                          </h4>
                          <p
                            style={{
                              fontSize: "14px",
                              color: colors.lightGray,
                              margin: 0,
                              lineHeight: "1.5",
                            }}
                          >
                            {item.description}
                          </p>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </Col>
              <Col xs={24} lg={12}>
                <div
                  style={{
                    padding: "0 8px",
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                    gap: "24px",
                  }}
                >
                  <div
                    style={{
                      background: `linear-gradient(135deg, ${colors.primary}, ${colors.secondary})`,
                      padding: "32px 24px",
                      borderRadius: "16px",
                      color: "white",
                      textAlign: "center",
                      width: "100%",
                      maxWidth: "400px",
                    }}
                  >
                    <div style={{ fontSize: "48px", marginBottom: "16px" }}>
                      📞
                    </div>
                    <h3
                      style={{
                        fontSize: "20px",
                        fontWeight: "700",
                        marginBottom: "12px",
                      }}
                    >
                      Emergency Pest Control
                    </h3>
                    <p
                      style={{
                        fontSize: "14px",
                        marginBottom: "24px",
                        opacity: 0.9,
                      }}
                    >
                      Available 24/7 for urgent pest control needs
                    </p>
                    <div
                      style={{
                        fontSize: "18px",
                        fontWeight: "700",
                        marginBottom: "20px",
                      }}
                    >
                      DIAL 0782381986 / 0712734834
                    </div>
                    <Button
                      size="large"
                      style={{
                        background: "white",
                        color: colors.primary,
                        fontWeight: "600",
                        height: "48px",
                        padding: "0 24px",
                        borderRadius: "8px",
                        border: "none",
                        fontSize: "14px",
                      }}
                    >
                      AND CONSIDER IT DONE!
                    </Button>
                  </div>
                  <div
                    style={{
                      width: "120px",
                      height: "120px",
                    }}
                  >
                    <Lottie
                      lottieRef={lottieRef}
                      animationData={mouseData}
                      loop={true}
                      autoplay={true}
                    />
                  </div>
                </div>
              </Col>
            </Row>
          </div>
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
              }}
            >
              TRUSTED BY INDUSTRY LEADERS
            </Tag>
            <Title
              level={2}
              style={{
                fontSize: "clamp(28px, 4vw, 42px)",
                fontWeight: "700",
                color: colors.dark,
                marginBottom: "16px",
              }}
            >
              What Our Clients Say
            </Title>
            <p
              style={{
                fontSize: "clamp(14px, 2vw, 16px)",
                color: colors.gray,
                maxWidth: "600px",
                margin: "0 auto",
                lineHeight: "1.6",
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
                      marginBottom: "12px",
                    }}
                  >
                    ❝
                  </div>
                  <p
                    style={{
                      fontSize: "15px",
                      color: colors.gray,
                      lineHeight: "1.5",
                      marginBottom: "20px",
                      fontStyle: "italic",
                    }}
                  >
                    "{testimonial.content}"
                  </p>
                  <div>
                    <div
                      style={{
                        fontSize: "16px",
                        fontWeight: "600",
                        color: colors.dark,
                        marginBottom: "4px",
                      }}
                    >
                      {testimonial.name}
                    </div>
                    <div
                      style={{
                        fontSize: "14px",
                        color: colors.primary,
                        fontWeight: "500",
                        marginBottom: "4px",
                      }}
                    >
                      {testimonial.company}
                    </div>
                    <div
                      style={{
                        fontSize: "13px",
                        color: colors.gray,
                        marginBottom: "8px",
                      }}
                    >
                      {testimonial.role}
                    </div>
                    <div
                      style={{
                        fontSize: "14px",
                        color: colors.secondary,
                        fontWeight: "500",
                      }}
                    >
                      📞 {testimonial.phone}
                    </div>
                  </div>
                </Card>
              </Col>
            ))}
          </Row>
        </div>
      </div>

      {/* Contact Section */}
      <div
        id="contact"
        className="section-padding"
        style={{
          padding: "80px 16px",
          background: colors.secondary,
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
          <Row gutter={[24, 24]}>
            <Col xs={24} md={12}>
              <div style={{ padding: "0 8px" }}>
                <Title
                  level={2}
                  style={{
                    fontSize: "clamp(28px, 4vw, 42px)",
                    fontWeight: "700",
                    color: "white",
                    marginBottom: "16px",
                  }}
                >
                  Get In Touch With Us
                </Title>
                <p
                  style={{
                    fontSize: "clamp(14px, 2vw, 16px)",
                    color: colors.lightGray,
                    lineHeight: "1.7",
                    marginBottom: "40px",
                  }}
                >
                  Ready to experience excellence in cleaning and pest control?
                  Contact us today for a free consultation and quote.
                </p>

                <div
                  style={{
                    display: "flex",
                    flexDirection: "column",
                    gap: "24px",
                  }}
                >
                  {[
                    // CALL SECTION
                    {
                      icon: (
                        <PhoneOutlined
                          style={{ fontSize: "20px", color: colors.primary }}
                        />
                      ),
                      title: "Call Us",
                      details: [
                        "+263 78 238 1986",
                        "+263 71 273 4834",
                        "Landline - 242328474/5",
                      ],
                      bgColor: colors.lightGray,
                    },

                    // WHATSAPP SECTION (separated)
                    {
                      icon: (
                        <WhatsAppOutlined
                          style={{ fontSize: "20px", color: "#25D366" }}
                        />
                      ),
                      title: "WhatsApp",
                      details: ["+263 78 238 1986", "+263 77 593 4109"],
                      isWhatsapp: true,
                      bgColor: colors.dark,
                    },

                    // EMAIL SECTION
                    {
                      icon: (
                        <MailOutlined
                          style={{ fontSize: "20px", color: "white" }}
                        />
                      ),
                      title: "Email Us",
                      details: [
                        "info@onpointhygienezw.co.zw",
                        "sales@onpointhygienezw.co.zw",
                        "elroy@onpointhygienezw.co.zw",
                        "richard@onpointhygienezw.co.zw",
                      ],
                      bgColor: colors.dark,
                    },

                    // ADDRESS SECTION
                    {
                      icon: (
                        <EnvironmentOutlined
                          style={{ fontSize: "20px", color: "white" }}
                        />
                      ),
                      title: "Visit Us",
                      details: ["26 Connaught Rd", "Avondale", "Harare"],
                      bgColor: colors.primary,
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
                          background: contact.bgColor,
                          borderRadius: "12px",
                          display: "flex",
                          alignItems: "center",
                          justifyContent: "center",
                          flexShrink: 0,
                        }}
                      >
                        {contact.icon}
                      </div>

                      <div>
                        <div
                          style={{
                            fontSize: "16px",
                            fontWeight: "600",
                            color: "white",
                            marginBottom: "8px",
                          }}
                        >
                          {contact.title}
                        </div>

                        {/* Render details */}
                        {contact.details.map((detail, i) => {
                          const cleaned = detail.replace(/\D/g, "");
                          const isWhatsapp = contact.isWhatsapp;

                          return (
                            <div
                              key={i}
                              style={{
                                fontSize: "14px",
                                color: colors.lightGray,
                                lineHeight: "1.4",
                              }}
                            >
                              {isWhatsapp ? (
                                <a
                                  href={`https://wa.me/${cleaned}`}
                                  style={{ color: colors.lightGray }}
                                  target="_blank"
                                  rel="noopener noreferrer"
                                >
                                  <WhatsAppOutlined
                                    style={{ marginRight: 6, color: "#25D366" }}
                                  />
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
              <div
                className="contact-form"
                style={{
                  background: "white",
                  padding: "32px 24px",
                  borderRadius: "12px",
                  boxShadow: "0 8px 24px rgba(0,0,0,0.08)",
                }}
              >
                <Title
                  level={3}
                  style={{
                    fontSize: "20px",
                    fontWeight: "600",
                    color: colors.dark,
                    marginBottom: "24px",
                  }}
                >
                  Send Us a Message
                </Title>
                <div
                  style={{
                    display: "flex",
                    flexDirection: "column",
                    gap: "16px",
                  }}
                >
                  <Input
                    placeholder="Your Name"
                    size="large"
                    value={contactForm.name}
                    onChange={(e) => handleInputChange("name", e.target.value)}
                    style={{
                      borderRadius: "8px",
                      border: "1px solid #E5E7EB",
                      height: "48px",
                    }}
                  />
                  <Input
                    placeholder="Your Email"
                    size="large"
                    value={contactForm.email}
                    onChange={(e) => handleInputChange("email", e.target.value)}
                    style={{
                      borderRadius: "8px",
                      border: "1px solid #E5E7EB",
                      height: "48px",
                    }}
                  />
                  <Input
                    placeholder="Your Phone"
                    size="large"
                    value={contactForm.phone}
                    onChange={(e) => handleInputChange("phone", e.target.value)}
                    style={{
                      borderRadius: "8px",
                      border: "1px solid #E5E7EB",
                      height: "48px",
                    }}
                  />
                  <TextArea
                    placeholder="Your Message"
                    rows={4}
                    value={contactForm.message}
                    onChange={(e) =>
                      handleInputChange("message", e.target.value)
                    }
                    style={{
                      borderRadius: "8px",
                      border: "1px solid #E5E7EB",
                      resize: "vertical",
                    }}
                  />
                  <Button
                    className="btn-primary"
                    size="large"
                    block
                    onClick={handleSubmit}
                    style={{ marginTop: "8px", height: "48px" }}
                  >
                    Send Message
                  </Button>
                </div>
              </div>
            </Col>
          </Row>
        </div>
      </div>

      {/* Footer */}
      <div
        style={{
          background: colors.dark,
          padding: "48px 16px 32px",
          color: "white",
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
          <Row gutter={[24, 24]}>
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
                      width: "42px",
                      height: "42px",
                      background: `linear-gradient(135deg, ${colors.primary}, ${colors.secondary})`,
                      borderRadius: "10px",
                      display: "flex",
                      alignItems: "center",
                      justifyContent: "center",
                      fontSize: "20px",
                      color: "white",
                    }}
                  >
                    ✨
                  </div>
                  <div>
                    <div style={{ fontSize: "18px", fontWeight: "700" }}>
                      Onpoint Hygiene & Fumigation Services
                    </div>
                    <div style={{ fontSize: "11px", color: "#9CA3AF" }}>
                      Consistency in Excellence
                    </div>
                  </div>
                </div>
                <p
                  style={{
                    fontSize: "14px",
                    color: "#9CA3AF",
                    lineHeight: "1.6",
                    marginBottom: "24px",
                  }}
                >
                  Internationally recognized cleaning and fumigation company
                  providing authentic services, products, and solutions across
                  Africa.
                </p>
                <div style={{ display: "flex", gap: "12px" }}>
                  {["f", "in", "X"].map((social, index) => (
                    <div
                      key={index}
                      style={{
                        width: "36px",
                        height: "36px",
                        background: "#374151",
                        borderRadius: "8px",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        cursor: "pointer",
                      }}
                    >
                      <span style={{ fontSize: "16px" }}>{social}</span>
                    </div>
                  ))}
                </div>
              </div>
            </Col>

            <Col xs={12} md={8}>
              <h4
                style={{
                  fontSize: "16px",
                  fontWeight: "600",
                  marginBottom: "16px",
                  color: "white",
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
                {[
                  "Cleaning Services",
                  "Hygienic Services",
                  "Pest Control",
                  "Fumigation",
                  "Sanitary Disposal",
                  "Emergency Services",
                ].map((service) => (
                  <a
                    key={service}
                    href="#services"
                    style={{
                      fontSize: "14px",
                      color: "#9CA3AF",
                      textDecoration: "none",
                      transition: "color 0.2s",
                    }}
                    onMouseEnter={(e) =>
                      (e.target.style.color = colors.primary)
                    }
                    onMouseLeave={(e) => (e.target.style.color = "#9CA3AF")}
                  >
                    {service}
                  </a>
                ))}
              </div>
            </Col>

            <Col xs={12} md={8}>
              <h4
                style={{
                  fontSize: "16px",
                  fontWeight: "600",
                  marginBottom: "16px",
                  color: "white",
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
                  "Our Team",
                  "Careers",
                  "Blog",
                  "Contact",
                  "Get Quote",
                ].map((item) => (
                  <a
                    key={item}
                    href="#about"
                    style={{
                      fontSize: "14px",
                      color: "#9CA3AF",
                      textDecoration: "none",
                      transition: "color 0.2s",
                    }}
                    onMouseEnter={(e) =>
                      (e.target.style.color = colors.primary)
                    }
                    onMouseLeave={(e) => (e.target.style.color = "#9CA3AF")}
                  >
                    {item}
                  </a>
                ))}
              </div>
            </Col>
          </Row>

          <Divider style={{ borderColor: "#374151", margin: "32px 0 24px" }} />

          <div style={{ textAlign: "center" }}>
            <p
              style={{
                fontSize: "14px",
                color: "#9CA3AF",
                margin: 0,
                lineHeight: "1.5",
              }}
            >
              © {new Date().getFullYear()} Onpoint Hygiene and Fumigation
              Services. All rights reserved. |
              <span style={{ color: colors.primary }}>
                {" "}
                AUTHENTIC SERVICES • AUTHENTIC PRODUCTS • AUTHENTIC SOLUTIONS
              </span>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OnpointLanding;
