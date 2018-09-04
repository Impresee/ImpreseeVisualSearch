var impreseeCropping = true;
require([
    "jquery",
    'Magento_Ui/js/modal/modal',
    'Impresee_ImpreseeVisualSearch/js/canvas',
    'Impresee_ImpreseeVisualSearch/js/photo',
], function ($, modal, canvas, photo) {
    'use strict';
    var impreseeCropping = true;
    var impreseeFirstCropArea = false;
    var impreseeFirstCrop = true;
    var buttonClasses = "btn btn-default";
    $(document).ready(function () {
        /** open modal on click sketch'search button*/
        $(".impresee-sketch-button").click(function () {
            showSketchModal($);
            $("#canvasDiv").parent().css("width","100%");
        });
        /** trigger file dialog on photo search impresee-photo-button impreseeButton */
        $(".impresee-photo-button").click(function () {
            $("#imguploadphoto").trigger('click');
        });
        /** reset file input value*/
        $("#imguploadphoto").click(function (event) {
            $("#imguploadphoto").val("");
        });
        /**send photo search query*/
        $("#imguploadphoto").on('change', function (event) {
            showPhotoModal($);
            $("#canvasDiv").parent().css("width","100%");
            getOrientation(this, function (input, orientation) {
                photoOrientation = orientation;
                loadImage(input);
                impreseeCropping = true;
            });
        });
        /** adjust on resize*/
        $(".jcrop_holder").bind('resize', function (e) {
            $("#canvasDiv").height($(".jcrop_holder").height());
            $(".modal-content").css("margin-bottom", "2%");
        });
    });
    /**
     * show modal windows with the canvas to draw in it
     * @param jquery object $
     */
    function showSketchModal($)
    {
        var options = {
            type: 'popup',
            responsive: true,
            modalLeftMargin: 0,
            innerScroll: false,
            title: 'Sketch Search',
            // buttons on modal
            buttons: [{
                text: $.mage.__('Search'),
                class: buttonClasses,
                click: function () {
                    var body = $('body').loader();
                    body.loader('show');
                    var canvas = document.getElementById('canvasSignature');
                    makeSearch("sketch", canvas.toDataURL("image/png"));
                    this.closeModal();
                }
            }, {
                text: $.mage.__('Clear'),
                class: buttonClasses,
                click: function () {
                    var canvas = document.getElementById('canvasSignature');
                    canvas.width = canvas.width;
                }
            }, ],
            closed: function (e) {
                $("#canvasDiv").remove();
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    $('html').css("overflow", "visible");
                    $('body').css("position", "static");
                }
                $("#modalDiv").append('<div id="canvasDiv" style="display:none; margin: 0 auto">');
                $(".modal-slide").remove();
            }
        };
        /** move canvasdiv to modal and add the canvas*/
        $("#canvasDiv").height(window.innerHeight * 0.7 + "px");
        $("#canvasDiv").width("95%");
        $("#canvasDiv").modal(options).modal('openModal');
        $('#canvasDiv').append('<canvas id=\"canvasSignature\" style=\"background-color:white; border:1px solid #000000; cursor:crosshair; margin: 0 auto;\"></canvas>');
        $("#canvasDiv").show();
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            $('body').css("position", "fixed");
            $('html').css("overflow", "hidden");
        }
        $(".modal-inner-wrap").css("margin-top", "0");
        $(".modal-inner-wrap").css("max-heigth", window.innerHeight + "px");
        var height = $('#canvasDiv').height() * 0.9;
        var width = $('#canvasDiv').width() * 0.9;
        var canvas = document.getElementById('canvasSignature');
        canvas.height = height;
        canvas.width = width;
        var context = canvas.getContext("2d");
        //add white background on canvas
        context.fillStyle = "#FFFFFF";
        context.fillRect(0, 0, width, height);
        setDrawableCanvas(canvas, context);
    }
    /**
     * show modal windows with selected image
     * @param jquery object $
     */
     function showPhotoModal($)
     {
         var options = {
             type: 'popup',
             responsive: true,
             modalLeftMargin: 0,
             innerScroll: false,
             title: "Photo Search",
             // buttons on modal
             buttons: [{
                     text: $.mage.__('Search'),
                     class: buttonClasses,
                     click: function () {
                         impreseeFirstCropArea = true;
                         var body = $('body').loader();
                         body.loader('show');
                         var canvas = document.getElementById('canvas');
                         if (impreseeCropping) {
                           applyCrop();
                         } else {
                           reduceCanvas(canvas.toDataURL("image/png"));
                         }
                         this.closeModal();
                     }
                 }, {
                     text: $.mage.__('Change Photo'),
                     class: buttonClasses,
                     click: function () {
                         $("#imguploadphoto").trigger('click');
                     }
                 }
             ],
             closed: function (e) {
                 $("#canvasDiv").remove();
                 $("#canvasDiv").css("display", "none");
                 if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                     $('html').css("overflow", "visible");
                     $('body').css("position", "static");
                 }
                 $("#modalDiv").append('<div id="canvasDiv" style="display:none; margin: 0 auto">');
                 $("aside._show").remove();
             }
         };
         $("#canvasDiv").height(window.innerHeight * 0.7 + "px");
         $("#canvasDiv").width("95%");
         $("#canvasDiv").css("display","table-cell");
         $("#canvasDiv").css("vertical-align","middle");
         $("#canvasDiv").modal(options).modal('openModal');
         $("#canvasDiv").show();

         // initialize jcrop


         if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
             $('body').css("position", "fixed");
             $('html').css("overflow", "hidden");
         }
     }
    /**
     * Min and max size of a cropped image
     */
    var crop_max_width = window.innerWidth * 0.6;
    var crop_max_height = window.innerHeight * 0.6;
    /**
     * Jcrop library object
     */
    var jcrop_api;
    /**
     * Canvas Dom element
     */
    var canvas;
    /**
     * Context of canvas
     */
    var context;
    /**
     * Image element
     */
    var image;
    /**
     * Orientation of the image loaded
     */
    var photoOrientation;
    /**
     * Object with the size of the crop
     */
    var prefsize;
    /**
     * Loads an image
     * @param input element where image is uploaded
     */
    function loadImage(input)
    {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            canvas = null;
            reader.onload = function (e) {
                image = new Image();
                image.onload = validateImage;
                image.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    /**
     * Convert image to blob
     * @param DOMstring with data uri
     */
    function dataURLtoBlob(dataURL)
    {
        var BASE64_MARKER = ';base64,';
        if (dataURL.indexOf(BASE64_MARKER) == -1) {
            var parts = dataURL.split(',');
            var contentType = parts[0].split(':')[1];
            var raw = decodeURIComponent(parts[1]);
            return new Blob([raw], {
                type: contentType
            });
        }
        var parts = dataURL.split(BASE64_MARKER);
        var contentType = parts[0].split(':')[1];
        var raw = window.atob(parts[1]);
        var rawLength = raw.length;
        var uInt8Array = new Uint8Array(rawLength);
        for (var i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }
        return new Blob([uInt8Array], {
            type: contentType
        });
    }
    /**
     * Validate if an image was loaded
     */
    function validateImage()
    {
        if (canvas != null) {
            image = new Image();
            image.onload = restartJcrop;
            image.src = canvas.toDataURL('image/png');
        } else {
restartJcrop(); }
    }
    /**
     * Restart Jcrop params
     */
    function restartJcrop()
    {
        impreseeCropping = true;
        if (jcrop_api != null) {
            jcrop_api.destroy();
        }
        $("#canvasDiv").empty();
        $("#canvasDiv").append('<canvas id="canvas" style="background-color:white;">');
        canvas = $("#canvas")[0];
        context = canvas.getContext("2d");
        switch (photoOrientation) {
            case 6:
                canvas.height = image.width;
                canvas.width = image.height;
                // move to the center of the canvas
                context.translate(canvas.width / 2, canvas.height / 2);
                // rotate the canvas to the specified degrees
                context.rotate(90 * Math.PI / 180);
                // draw the image
                // since the context is rotated, the image will be rotated also
                context.drawImage(image, -canvas.height / 2, -canvas.width / 2, canvas.height, canvas.width);
                break;
            case 7:
                canvas.height = image.width;
                canvas.width = image.height;
                // move to the center of the canvas
                context.translate(canvas.width / 2, canvas.height / 2);
                // rotate the canvas to the specified degrees
                context.rotate(270 * Math.PI / 180);
                // draw the image
                // since the context is rotated, the image will be rotated also
                context.drawImage(image, -canvas.height / 2, -canvas.width / 2, canvas.height, canvas.width);
                break;
            default:
                canvas.width = image.width;
                canvas.height = image.height;
                var context = canvas.getContext("2d");
                context.drawImage(image, 0, 0, image.width, image.height);
        }
        $("#canvas").Jcrop({
            onSelect: selectcanvas,
            onRelease: clearcanvas,
            boxWidth: crop_max_width,
            boxHeight: crop_max_height,
            // addClass: 'jcropClass',
            setSelect: [canvas.width * 0.05, canvas.height * 0.05, canvas.width * 0.95, canvas.height*0.95]
        }, function () {
            jcrop_api = this;
        });
        if (impreseeFirstCrop) {
          impreseeFirstCrop = false;
          var coords = {
            x : canvas.width * 0.05,
            y : canvas.height * 0.05,
            w : canvas.width * 0.95,
            h : canvas.height*0.95
          }
          selectcanvas(coords);
        }
        if (impreseeCropping && impreseeFirstCropArea) {
          reduceCanvas(canvas.toDataURL("image/src"));
        }
        clearcanvas();
        impreseeFirstCropArea = false;
      }
    /**
     * Clear the canvas where image is displayed
     */
     function clearcanvas()
     {
         impreseeCropping = true;
         prefsize = {
             x: 0,
             y: 0,
             w: canvas.width,
             h: canvas.height,
         };
     }
    /**
     * Save on prefsize the selected area to crop
     * @param coords JScript object with coords of area
     */
    function selectcanvas(coords)
    {
        prefsize = {
            x: Math.round(coords.x),
            y: Math.round(coords.y),
            w: Math.round(coords.w),
            h: Math.round(coords.h)
        };
        impreseeCropping = true;
    }
    /**
     * Apply Crop to image in selected area
     */
    function applyCrop()
    {
        context = canvas.getContext("2d");
        switch (photoOrientation) {
            case 6:
                canvas.width = prefsize.h;
                canvas.height = prefsize.w;
                context.drawImage(image, prefsize.y, (image.height - (prefsize.w + prefsize.x)), prefsize.h, prefsize.w, 0, 0, canvas.width, canvas.height);
                break;
            case 7:
                canvas.width = prefsize.h;
                canvas.height = prefsize.w;
                context.drawImage(image, prefsize.y, (image.height - (prefsize.w + prefsize.x)), prefsize.h, prefsize.w, 0, 0, canvas.width, canvas.height);
                break;
            default:
                canvas.width = prefsize.w;
                canvas.height = prefsize.h;
                context.drawImage(image, prefsize.x, prefsize.y, prefsize.w, prefsize.h, 0, 0, canvas.width, canvas.height);
        }
        validateImage();
    }
});
