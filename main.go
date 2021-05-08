package main

import (
	"fmt"
	"log"
	"os"

	"github.com/gofiber/fiber/v2"
	"github.com/gofiber/fiber/v2/middleware/logger"
	"github.com/kkamara/go-ecommerce/engine"
	"github.com/kkamara/go-ecommerce/handlers/home"
	productRoutes "github.com/kkamara/go-ecommerce/handlers/product"
	"github.com/kkamara/go-ecommerce/models/company"
	"github.com/kkamara/go-ecommerce/models/company/vendor_application"
	"github.com/kkamara/go-ecommerce/models/order"
	"github.com/kkamara/go-ecommerce/models/order/order_product"
	"github.com/kkamara/go-ecommerce/models/product"
	"github.com/kkamara/go-ecommerce/models/product/flagged_review"
	"github.com/kkamara/go-ecommerce/models/product/product_review"
	"github.com/kkamara/go-ecommerce/models/user"
	"github.com/kkamara/go-ecommerce/models/user/address"
	"github.com/kkamara/go-ecommerce/models/user/payment"
)

func Seed() (err error) {
	type modelType func() error
	models := []modelType{
		user.Seed,
		company.Seed,
		vendor_application.Seed,
		product.Seed,
		product_review.Seed,
		flagged_review.Seed,
		order.Seed,
		order_product.Seed,
		address.Seed,
		payment.Seed,
	}
	for _, m := range models {
		err = m()
		if err != nil {
			return
		}
	}
	return
}

func main() {
	port := os.Getenv("PORT")
	if port == "" {
		port = "3000"
	}
	app := *fiber.New(fiber.Config{
		Views: engine.GetEngine(),
	})

	app.Use(logger.New())

	err := Seed()
	if err != nil {
		panic(err)
	}

	app.Static("/", "resources")

	app.Get("/", home.IndexHandler)
	app.Get("/products", productRoutes.IndexHandler)

	log.Fatal(app.Listen(fmt.Sprintf(":%s", port)))
}
