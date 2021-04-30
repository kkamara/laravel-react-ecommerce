package home

import (
	"github.com/gofiber/fiber/v2"
	"github.com/kkamara/laravel-ecommerce/models/product"
)

func GetHomeHandler(c *fiber.Ctx) error {
	products, err := product.GetProducts(
		c.Query("page", "1"),
		c.Query("page_size", "10"),
	)
	if err != nil {
		c.Context().SetStatusCode(500)
		return c.JSON(fiber.Map{"error": "Failed to fetch products."})
	}

	return c.Render("index", fiber.Map{
		"Title":    "Hello, World!",
		"Products": products,
	}, "layouts/master")
}